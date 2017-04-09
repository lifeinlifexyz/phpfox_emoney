<?php

namespace Apps\CM_ElMoney\Controller;

use Phpfox;
use Phpfox_Plugin;

class AddFunds extends \Phpfox_Component
{

    public function process()
    {
        Phpfox::isUser(true);

        $sUserCurrency = Phpfox::getService('user')->getCurrency();
        $oSettings =  Phpfox::getService('elmoney.settings');

        $this->template()
            ->setTitle(_p('Add funds to your account'))
            ->setBreadCrumb(_p('Add funds to your account'), '', true)
            ->assign([
                'aSettings' => Phpfox::getService('elmoney.settings')->all(),
                'sUserExchangeRate' => Phpfox::getPhrase('elmoney.elmoney_user_exchange_rate_message',
                    [
                        'user_currency' => $sUserCurrency,
                        'elmoney_currency' => $oSettings['currency_code'],
                        'exchange_rate' => Phpfox::getService('core.currency')->getSymbol($sUserCurrency) . $oSettings['exchange_rate_' . $sUserCurrency],
                    ]),
                'aCommission' => $oSettings['commissions'],
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
        (($sPlugin = Phpfox_Plugin::get('api.component_controller_elmoney_profile_clean')) ? eval($sPlugin) : false);
    }
}