<?php

\Phpfox_Module::instance()
    ->addServiceNames([
        'elmoney.settings' => 'Apps\CM_ElMoney\Service\Settings',
    ])
    ->addComponentNames('controller', [
        'elmoney.admincp.gateway.settings' => 'Apps\CM_ElMoney\Controller\Admin\Gateway\Settings',
        'elmoney.admincp.settings' => 'Apps\CM_ElMoney\Controller\Admin\Settings',
    ])
    ->addAliasNames('elmoney', 'CM_ElMoney')
    ->addTemplateDirs([
        'elmoney' => PHPFOX_DIR_SITE_APPS . 'CM_ElMoney' . PHPFOX_DS . 'views',
    ]);

group('/admincp/elmoney/', function(){
    route('gateway/settings', 'elmoney.admincp.gateway.settings');
    route('settings', 'elmoney.admincp.settings');
});

//
//defined('CM_CASH_PAYMENT_ACTIVE') or define('CM_CASH_PAYMENT_ACTIVE', Phpfox::getService('elmoney')->isActive());
//
group('/elmoney/', function(){
    route('gateway/setting/save', 'elmoney.admincp.gateway.settings');
    route('setting/save', 'elmoney.admincp.settings');

//    if (CM_CASH_PAYMENT_ACTIVE) {
//        route('buy', 'elmoney.buy');
//        route('info', 'elmoney.buy');
//        route('endorse/profile', 'elmoney.endorse');
//        route('profile', 'elmoney.profile');
//    }

});
