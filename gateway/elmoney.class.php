<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Phpfox_Gateway_Api_ElMoney implements Phpfox_Gateway_Interface
{
	/**
	 * Holds an ARRAY of settings to pass to the form
	 *
	 * @var array
	 */
	private $_aParam = array();


	/**
	 * Class constructor
	 *
	 */
	public function __construct()
	{

	}

	/**
	 * Set the settings to be used with this class and prepare them so they are in an array
	 *
	 * @param array $aSetting ARRAY of settings to prepare
	 */
	public function set($aSetting)
	{
		$this->_aParam = $aSetting;

		if (Phpfox::getLib('parse.format')->isSerialized($aSetting['setting']))
		{
			$this->_aParam['setting'] = unserialize($aSetting['setting']);
		}
	}

	/**
	 * Each gateway has a unique list of params that must be passed with the HTML form when posting it
	 * to their site. This method creates that set of custom fields.
	 *
	 * @return array ARRAY of all the custom params
	 */
	public function getEditForm()
	{
		return [
			'elmoney_seller_id' => [
				'phrase' => _p('Do enable EL Money for you?'),
				'phrase_info' => _p('Any symbol for enable, empty for disable'),
				'value' => Phpfox::getUserId()
			]
		];
	}

	/**
	 * Returns the actual HTML <form> used to post information to the 3rd party gateway when purchasing
	 * an item using this specific payment gateway
	 *
	 * @return bool FALSE if we can't use this payment gateway to purchase this item or ARRAY if we have successfully created a form
	 */
	public function getForm()
	{
		if (isset($this->_aParam['setting']['elmoney_seller_id']) && $this->_aParam['setting']['elmoney_seller_id']) {
			$aForm = [
				'url' => Phpfox_Url::instance()->makeUrl('elmoney.pay'),
				'param' => [
					'elmoney_seller_id' => $this->_aParam['setting']['elmoney_seller_id'],
					'buyer_id' => Phpfox::getUserId(),
					'item_name' => $this->_aParam['item_name'],
					'item_number' => $this->_aParam['item_number'],
					'currency_code' => $this->_aParam['currency_code'],
					'return' => $this->_aParam['return'],
				]
			];
			//convert to el money
			$aForm['param']['amount'] = Phpfox::getService('elmoney')->convertTo($this->_aParam['amount'], $this->_aParam['currency_code']);
			return $aForm;
		} else {
			return false;
		}
	}

	/**
	 * Performs the callback routine when the 3rd party payment gateway sends back a request to the server,
	 * which we must then back and verify that it is a valid request. This then connects to a specific module
	 * based on the information passed when posting the form to the server.
	 *
	 */
	public function callback()
	{
		Phpfox::log('Starting EL Money callback');

		Phpfox::log('Attempting callback');

		Phpfox::log('Callback OK');
		$isApp = false;
		$aParts = explode('|', $this->_aParam['item_number']);
		if (substr($aParts[0], 0, 5) == '@App/') {
			$isApp = true;
			Phpfox::log('Is an APP.');
		}

		Phpfox::log('Attempting to load module: ' . $aParts[0]);

		if ($isApp || Phpfox::isModule($aParts[0]))
		{
			Phpfox::log('Module is valid.');
			Phpfox::log('Checking module callback for method: paymentApiCallback');
			if ($isApp || (Phpfox::isModule($aParts[0]) && Phpfox::hasCallback($aParts[0], 'paymentApiCallback')))
			{
				Phpfox::log('Module callback is valid.');
				$iAmount = Phpfox::getService('elmoney')->convertFrom($this->_aParam['amount'], $this->_aParam['currency_code']);
				$sStatus = 'completed';

				Phpfox::log('Status built: ' . $sStatus);

				Phpfox::log('Executing module callback');

				$params = array(
					'gateway' => 'EL Money',
					'ref' => $this->_aParam['item_number'],
					'status' => $sStatus,
					'item_number' => $aParts[1],
					'total_paid' => $iAmount,
				);

				if ($isApp) {
					$callback = str_replace('@App/', '', $aParts[0]);
					Phpfox::log('Running app callback on: ' . $callback);
					\Core\Payment\Trigger::event($callback, $params);
				} else {
					Phpfox::callback($aParts[0] . '.paymentApiCallback', $params);
				}

				Phpfox::log('Reduce balance from buyer: ' . var_export([
						'buyer' => $this->_aParam['buyer_id'],
						'amount' =>  $this->_aParam['amount'],
					], true));

				$iBuyerBalance = Phpfox::getService('elmoney')->reduceBalance($this->_aParam['buyer_id'], $this->_aParam['amount']);

				Phpfox::log('Add balance to seller: ' . var_export([
						'buyer' => $this->_aParam['elmoney_seller_id'],
						'amount' =>  $this->_aParam['amount'],
					], true));

				$this->_aParam['amount'] = $this->_aParam['amount'] - Phpfox::getService('elmoney')
						->getCommission($this->_aParam['amount'], \Apps\CM_ElMoney\Service\ElMoney::COMMISSION_SALE);

				$sAffiliateCode = Phpfox::getLib('session')->get('elmoney_affiliate_code');

				if (!empty($sAffiliateCode)) {

					Phpfox::log('Affiliate code exits');
					$aAffiliate  = Phpfox::getService('elmoney.affiliate')->getAffiliateByCode($sAffiliateCode);
					if (!empty($aAffiliate)) {
						Phpfox::log('Affiliate: ' . var_export($aAffiliate, true));
						$iAffiliateAmount = $this->_aParam['amount'] * ($aAffiliate['percent'] / 100);
						$this->_aParam['amount'] = $this->_aParam['amount'] - $iAffiliateAmount;
						Phpfox::getService('elmoney.affiliate')->add([
							'code_id' => $aAffiliate['code_id'],
							'user_id' => $this->_aParam['buyer_id'],
							'owner_id' => $aAffiliate['user_id'],
							'amount' => $iAffiliateAmount,
							'transaction_id' => $this->_aParam['tr_id'],
							'seller_id' => $this->_aParam['elmoney_seller_id'],
						]);
						Phpfox::getService('elmoney')->addBalanceToUser($aAffiliate['user_id'], $iAffiliateAmount);
					} else {
						Phpfox::log('Affiliate not found');
					}
					Phpfox::getLib('session')->remove('elmoney_affiliate_code');
				}

				$iSellerBalance = Phpfox::getService('elmoney')->addBalanceToUser($this->_aParam['elmoney_seller_id'], $this->_aParam['amount']);
				$aTransactionVals =  [
					'status' => 'completed',
					'buyer_balance' =>  $iBuyerBalance,
					'seller_balance' =>  $iSellerBalance,
					'time_stamp' => PHPFOX_TIME,
				];
				if (isset($iAffiliateAmount)) {

				}
				Phpfox::getService('elmoney.trunsaction')->update($this->_aParam['tr_id'], $aTransactionVals);
			}
			else
			{
				Phpfox::log('Module callback is not valid.');
			}
		}
		else
		{
			Phpfox::log('Module is not valid.');
		}

	}
}