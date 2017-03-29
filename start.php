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
        'elmoney.admincp.funds.manage' => 'Apps\CM_ElMoney\Controller\Admin\ManageFunds',
    ])->addComponentNames('ajax', [
        'elmoney.ajax'        => '\Apps\CM_ElMoney\Ajax\Ajax',
    ])
    ->addAliasNames('elmoney', 'CM_ElMoney')
    ->addTemplateDirs([
        'elmoney' => PHPFOX_DIR_SITE_APPS . 'CM_ElMoney' . PHPFOX_DS . 'views',
    ]);


/**
 * template modifier
 */
function el_money_currency($iAmount) {
    return Phpfox::getService('elmoney')->currency($iAmount);
}

group('/admincp/elmoney/', function(){
    route('gateway/settings', 'elmoney.admincp.gateway.settings');
    route('settings', 'elmoney.admincp.settings');
    route('funds/manage', 'elmoney.admincp.funds.manage');
});

defined('CM_EL_MONEY_IS_ACTIVE') or define('CM_EL_MONEY_IS_ACTIVE', Phpfox::getService('elmoney')->isActive());

if(CM_EL_MONEY_IS_ACTIVE) {
    Phpfox_Module::instance()->addComponentNames('controller', [
        'elmoney.profile' => 'Apps\CM_ElMoney\Controller\Profile',
        'elmoney.pay' => 'Apps\CM_ElMoney\Controller\Pay',
        'elmoney.funds.add' => 'Apps\CM_ElMoney\Controller\AddFunds',
        'elmoney.sendtofriend' => 'Apps\CM_ElMoney\Controller\SendToFriend',
    ])
    ->addComponentNames('block', [
        'elmoney.balance' => 'Apps\CM_ElMoney\Block\Balance',
    ]);
}

group('/elmoney/', function(){
    route('gateway/setting/save', 'elmoney.admincp.gateway.settings');
    route('setting/save', 'elmoney.admincp.settings');
    route('admincp/funds/add', 'elmoney.admincp.funds.add');

    if (CM_EL_MONEY_IS_ACTIVE) {
        route('profile', 'elmoney.profile');
        route('profile.purchase', 'elmoney.profile');
        route('sendtofriend', 'elmoney.sendtofriend');
        route('funds/add', 'elmoney.funds.add');
        route('pay', 'elmoney.pay');
    }

});
