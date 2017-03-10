<?php

namespace Apps\CM_ElMoney\Controller\Admin;


use Phpfox;
use Phpfox_Plugin;

class Settings extends \Phpfox_Component
{
    public function process()
    {
        Phpfox::isAdmin(true);

        $aValidate = [
            'currency_code' =>  _p('Currency code is required'),
        ];
        /**
         * @var $oSetting \Apps\CM_ElMoney\Service\Settings
         */
        $oSetting = Phpfox::getService('elmoney.settings');
        $aForms = [
            'currency_code' => $oSetting['currency_code'],
        ];

        $aCurrencies = Phpfox::getService('core.currency')->get();

        foreach($aCurrencies as $sCurrencyId => $aCurrency) {
            $aCurrency['var'] = 'exchange_rate_' . $sCurrencyId;

            $aValidate[$aCurrency['var']] = [
                'title' => _p('Exchange rate must be a valid money format(ex: 1.00)', ['currency' => $sCurrencyId]),
                'pattern' => '/^\d{1,}([\.]\d{1,})$/',
            ];

            $aForms[$aCurrency['var']] = $oSetting[$aCurrency['var']] ?: '1.00';
            $aCurrencies[$sCurrencyId] = $aCurrency;
        }

        $oValidator = \Phpfox_Validator::instance();
        $oValidator->set([
            'sFormName' => 'js_elmoney_setting',
            'aParams' => $aValidate,
        ]);

        if (($aVals = $this->request()->getArray('val')))
        {
            $aForms = $aVals;
            if ($oValidator->isValid($aVals)) {
                Phpfox::getService('elmoney.settings')->save($aVals);
                $this->url()->send('admincp.app',
                    [
                        'id' => 'CM_ElMoney',
                    ], _p('Settings saved'));
            }
        }

        $this->template()
            ->setTitle(_p('Settings'))
            ->assign([
                    'aCurrencies' => $aCurrencies,
                    'aForms' => $aForms,
                ]
            );
        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('api.component_controller_admincp_elmoney_settings_clean')) ? eval($sPlugin) : false);
    }
}