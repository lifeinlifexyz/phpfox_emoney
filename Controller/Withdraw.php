<?php

namespace Apps\CM_ElMoney\Controller;

use Phpfox;
use Phpfox_Plugin;

class Withdraw extends \Phpfox_Component
{

    public function process()
    {
        Phpfox::isUser(true);
        $oSettings =  Phpfox::getService('elmoney.settings');

        if (!$oSettings['withdraw']) {
            $this->url()->send('profile.elmoney');
        }

        $aUserGateways = Phpfox::getService('api.gateway')->getUserGateways(Phpfox::getUserId());
        $aActiveGateways = Phpfox::getService('api.gateway')->getActive();
        $aGateway = [];
        if (is_array($aUserGateways) && count($aUserGateways)) {

            foreach ($aUserGateways as $sGateway => $aData) {

                // Payment gateways added after user configured their payment gateway settings
                if(empty($aActiveGateways)) {
                    continue;
                }

                foreach ($aActiveGateways as $aActiveGateway) {
                    if($sGateway == $aActiveGateway['gateway_id']) {
                        $aGateway[$aActiveGateway['gateway_id']] = $aActiveGateway;
                    }
                }
            }
        }

        if (isset($aGateway['elmoney'])) {
            unset($aGateway['elmoney']);
        }

        $aValidation =  [
            'gateway' => _p('Gateway is required'),
            'amount' => _p('Amount Id is required'),
        ];
        $aVals  =  $this->request()->getArray('val');

        $sUserCurrency = Phpfox::getService('user')->getCurrency();

        if ($_POST) {
            /**
             * @var $oValidator \Phpfox_Validator
             */
            $oValidator  = \Phpfox_Validator::instance()->set([
                'sFormName' => 'js_el_money',
                'aParams' => $aValidation,
            ]);

            if ($oValidator->isValid($aVals)) {
                $bIsValid = true;
                $iUserBalance = Phpfox::getService('elmoney')->getUserBalance();
                $iCommission = Phpfox::getService('elmoney')->getCommission($aVals['amount'], 'withdraw');
                $iTotalAmount = $aVals['amount'] - $iCommission;
                if ($iUserBalance < $iTotalAmount) {
                    \Phpfox_Error::set(_p('You do not have enough money'));
                    $bIsValid = false;
                }
                $aVals['commission']  = $iCommission;
                $aVals['currency']  = $sUserCurrency;

                if ($bIsValid) {
                    Phpfox::getService('elmoney.withdraw')->add($aVals);
                    $this->url()->send('elmoney.withdraw.history', [], _p('Successfully sent'));
                }

            }
        }



        $this->template()->buildSectionMenu('profile.elmoney', Phpfox::getService('elmoney')->getSectionMenu());
        $this->template()->setTitle(_p('Withdraw'))
            ->setBreadCrumb(_p('Withdraw'))
            ->assign([
                'aForms' => $aVals,
                'aGateways' => $aGateway,
                'aCommission' => $oSettings['commissions'],
                'sUserExchangeRate' => Phpfox::getPhrase('elmoney.elmoney_user_exchange_rate_message',
                    [
                        'user_currency' => $sUserCurrency,
                        'elmoney_currency' => $oSettings['currency_code'],
                        'exchange_rate' => Phpfox::getService('core.currency')->getSymbol($sUserCurrency) . $oSettings['exchange_rate_' . $sUserCurrency],
                    ]),
                'iExchangeRate' => $oSettings['exchange_rate_' . $sUserCurrency],
                'sCurrency' => Phpfox::getService('core.currency')->getSymbol($sUserCurrency),
            ]);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('api.component_controller_elmoney_pay_clean')) ? eval($sPlugin) : false);
    }
}