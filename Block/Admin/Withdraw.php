<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\CM_ElMoney\Block\Admin;

use Api_Service_Gateway_Gateway;
use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox_Component
 * @version 		$Id: form.class.php 7107 2014-02-11 19:46:17Z Fern $
 */
class Withdraw extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$aWithdrawData = $this->getParam('aData');
		$iUserId = $aWithdrawData['user_id'];
		$aUserGateways = Phpfox::getService('api.gateway')->getUserGateways($iUserId);
		$aActiveGateways = Phpfox::getService('api.gateway')->getActive();
		$aPurchaseDetails = [
			'item_number' => 'elmoney|wd_' . $aWithdrawData['withdraw_id'],
			'currency_code' => $aWithdrawData['currency'],
			'amount' => $aWithdrawData['withdraw'],
			'return' => url()->makeUrl('elmoney.withdraw.history', ['payment' => 'done']),
			'item_name' => _p('Withdraw El Money'),
			'recurring' => '',
			'recurring_cost' => '',
			'alternative_cost' => '',
			'alternative_recurring_cost' => '',
		];
		if (is_array($aUserGateways) && count($aUserGateways))
		{
			foreach ($aUserGateways as $sGateway => $aData)
			{
				if ($sGateway != $aWithdrawData['gateway']) {
					$aPurchaseDetails['fail_' . $sGateway] = true;
				}

				if (is_array($aData['gateway']))
				{
					foreach ($aData['gateway'] as $sKey => $mValue)
					{
						$aPurchaseDetails['setting'][$sKey] = $mValue;
					}
				}
				else
				{
					$aPurchaseDetails['fail_' . $sGateway] = true;
				}

				// Payment gateways added after user configured their payment gateway settings
				if(empty($aActiveGateways))
				{
					continue;
				}
				$bActive = false;
				foreach ($aActiveGateways as $aActiveGateway)
				{
					if($sGateway == $aActiveGateway['gateway_id'])
					{
						$bActive = true;
					}
				}
				if(!$bActive)
				{
					$aPurchaseDetails['fail_' . $aActiveGateway['gateway_id']] = true;
				}
			}
		}

		$this->template()->assign([
				'aGateways' => Api_Service_Gateway_Gateway::instance()->get($aPurchaseDetails),
				'aGatewayData' => $aPurchaseDetails,
				'bIsThickBox' => true
			]
		);
		return 'block';
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('api.component_block_list_clean')) ? eval($sPlugin) : false);
	}
}