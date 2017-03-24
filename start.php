<?php

\Phpfox_Module::instance()
    ->addServiceNames([
        'elmoney.settings' => 'Apps\CM_ElMoney\Service\Settings',
        'elmoney' => 'Apps\CM_ElMoney\Service\ElMoney',
        'elmoney.callback' => 'Apps\CM_ElMoney\Service\Callback',
        'elmoney.payments' => 'Apps\CM_ElMoney\Service\Payments',
        'elmoney.trunsaction' => 'Apps\CM_ElMoney\Service\Trunsaction',
        'elmoney.browse' => 'Apps\CM_ElMoney\Service\Browse',
    ])
    ->addComponentNames('controller', [
        'elmoney.admincp.gateway.settings' => 'Apps\CM_ElMoney\Controller\Admin\Gateway\Settings',
        'elmoney.admincp.settings' => 'Apps\CM_ElMoney\Controller\Admin\Settings',
    ])->addComponentNames('ajax', [
        'elmoney.ajax'        => '\Apps\CM_ElMoney\Ajax\Ajax',
    ])
    ->addAliasNames('elmoney', 'CM_ElMoney')
    ->addTemplateDirs([
        'elmoney' => PHPFOX_DIR_SITE_APPS . 'CM_ElMoney' . PHPFOX_DS . 'views',
    ]);

group('/admincp/elmoney/', function(){
    route('gateway/settings', 'elmoney.admincp.gateway.settings');
    route('settings', 'elmoney.admincp.settings');
});

defined('CM_EL_MONEY_IS_ACTIVE') or define('CM_EL_MONEY_IS_ACTIVE', Phpfox::getService('elmoney')->isActive());

if(CM_EL_MONEY_IS_ACTIVE) {
    Phpfox_Module::instance()->addComponentNames('controller', [
        'elmoney.profile' => 'Apps\CM_ElMoney\Controller\Profile',
        'elmoney.pay' => 'Apps\CM_ElMoney\Controller\Pay',
        'elmoney.funds.add' => 'Apps\CM_ElMoney\Controller\AddFunds',
    ])
    ->addComponentNames('block', [
        'elmoney.balance' => 'Apps\CM_ElMoney\Block\Balance',
    ]);
}

group('/elmoney/', function(){
    route('gateway/setting/save', 'elmoney.admincp.gateway.settings');
    route('setting/save', 'elmoney.admincp.settings');

    if (CM_EL_MONEY_IS_ACTIVE) {
        route('profile', 'elmoney.profile');
        route('profile.purchase', 'elmoney.profile');
        route('funds/add', 'elmoney.funds.add');
        route('pay', 'elmoney.pay');
    }

});
