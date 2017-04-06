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
        $sCommission = $oSetting['commissions'];
        $sAffiliate = $oSetting['affiliate'];
        $aAffiliate = [];

        if (Phpfox::isModule('subscribe')) {
            $aAffiliate['subscribe'] = [
                'title' => _p('Subscription affiliate'),
                'percent' => 0,
            ];
        }

        $aForms = [
            'currency_code' => $oSetting['currency_code'],
            'commissions' => empty($sCommission) ? [
                'add_funds' => [''],
                'send_to_friend' => [''],
                'withdraw' => [''],
                'sale' => [''],
            ] : json_decode($oSetting['commissions'], true),
            'withdraw' => $oSetting['withdraw'],
            'affiliate' => empty($sAffiliate)
                ? $aAffiliate
                : json_decode($oSetting['affiliate'], true),
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

        (($sPlugin = Phpfox_Plugin::get('elmoney.setting_form_set')) ? eval($sPlugin) : false);

        $oValidator = \Phpfox_Validator::instance();
        $oValidator->set([
            'sFormName' => 'js_elmoney_setting',
            'aParams' => $aValidate,
        ]);

        if (($aVals = $this->request()->getArray('val'))) {
            $aNewCommissions = $aVals['commissions']['add_funds'];
            foreach($aNewCommissions as $sCommission) {
                if (!preg_match('/^\d{1,}:\d{1,}\|\d{1,}$/', $sCommission)) {
                    \Phpfox_Error::set(_p('Commission format must be digit:digit|digit'));
                }
            }

            $aNewCommissions = $aVals['commissions']['sale'];
            foreach($aNewCommissions as $sCommission) {
                if (!preg_match('/^\d{1,}:\d{1,}\|\d{1,}$/', $sCommission)) {
                    \Phpfox_Error::set(_p('Commission format must be digit:digit|digit'));
                }
            }

            $aNewCommissions = $aVals['commissions']['send_to_friend'];
            foreach($aNewCommissions as $sCommission) {
                if (!preg_match('/^\d{1,}:\d{1,}\|\d{1,}$/', $sCommission)) {
                    \Phpfox_Error::set(_p('Commission format must be digit:digit|digit'));
                }
            }

            $aNewCommissions = $aVals['commissions']['withdraw'];
            foreach($aNewCommissions as $sCommission) {
                if (!preg_match('/^\d{1,}:\d{1,}\|\d{1,}$/', $sCommission)) {
                    \Phpfox_Error::set(_p('Commission format must be digit:digit|digit'));
                }
            }

            $aForms = $aVals;
            (($sPlugin = Phpfox_Plugin::get('elmoney.before_validate_save')) ? eval($sPlugin) : false);
            if ($oValidator->isValid($aVals)) {
                $aVals['commissions'] = json_encode($aVals['commissions']);
                $aVals['affiliate'] = json_encode($aVals['affiliate']);
                (($sPlugin = Phpfox_Plugin::get('elmoney.before_settings_save')) ? eval($sPlugin) : false);
                Phpfox::getService('elmoney.settings')->save($aVals);
                (($sPlugin = Phpfox_Plugin::get('elmoney.after_settings_save')) ? eval($sPlugin) : false);
                $this->url()->send('admincp.app',
                    [
                        'id' => 'CM_ElMoney',
                    ], _p('Settings saved'));
            }
        }

        $this->template()
            ->setPhrase([
                _p('Remove'),
            ])
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