<?php

if (version_compare(Phpfox::getVersion(), '4.5.2', '>=')) {
    require_once  PHPFOX_DIR_LIB_CORE . 'gateway' . PHPFOX_DS . 'api' . PHPFOX_DS . 'elmoney.class.php';
    Phpfox::getLibContainer()->set('gateway.api.elmoney', new \Phpfox_Gateway_Api_ElMoney());
}

\Phpfox_Module::instance()
    ->addServiceNames([
        'elmoney.settings' => 'Apps\CM_ElMoney\Service\Settings',
        'elmoney' => 'Apps\CM_ElMoney\Service\ElMoney',
        'elmoney.callback' => 'Apps\CM_ElMoney\Service\Callback',
        'elmoney.payments' => 'Apps\CM_ElMoney\Service\Payments',
        'elmoney.trunsaction' => 'Apps\CM_ElMoney\Service\Trunsaction',
        'elmoney.browse' => 'Apps\CM_ElMoney\Service\Browse',
        'elmoney.withdraw' => 'Apps\CM_ElMoney\Service\Withdraw',
        'elmoney.affiliate' => 'Apps\CM_ElMoney\Service\Affiliate',
    ])
    ->addComponentNames('controller', [
        'elmoney.admincp.gateway.settings' => 'Apps\CM_ElMoney\Controller\Admin\Gateway\Settings',
        'elmoney.admincp.settings' => 'Apps\CM_ElMoney\Controller\Admin\Settings',
        'elmoney.admincp.funds.manage' => 'Apps\CM_ElMoney\Controller\Admin\ManageFunds',
        'elmoney.admincp.withdraw' => 'Apps\CM_ElMoney\Controller\Admin\Withdraw',
    ])->addComponentNames('block', [
        'elmoney.admincp.withdraw' => '\Apps\CM_ElMoney\Block\Admin\Withdraw',
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
    route('withdraw', 'elmoney.admincp.withdraw');
});

defined('CM_EL_MONEY_IS_ACTIVE') or define('CM_EL_MONEY_IS_ACTIVE', Phpfox::getService('elmoney')->isActive());

if (!CM_EL_MONEY_IS_ACTIVE) {
    //\Phpfox::getService('admincp.module.process')->updateActivity('elmoney', 0); //for fix if gateway disabled
    db()->update(Phpfox::getT('menu'), ['is_active' => 0], '`url_value` = \'/elmoney\'');
}

Phpfox_Module::instance()->addComponentNames('controller', [
    'elmoney.index' => 'Apps\CM_ElMoney\Controller\Index',
    'elmoney.confirm' => 'Apps\CM_ElMoney\Controller\Confirm',
    'elmoney.funds.add' => 'Apps\CM_ElMoney\Controller\AddFunds',
    'elmoney.sendtofriend' => 'Apps\CM_ElMoney\Controller\SendToFriend',
    'elmoney.withdraw' => 'Apps\CM_ElMoney\Controller\Withdraw',
    'elmoney.withdraw.history' => 'Apps\CM_ElMoney\Controller\WithdrawHistory',
    'elmoney.affiliate.links' => 'Apps\CM_ElMoney\Controller\Affiliate\Links',
    'elmoney.affiliate.statistics' => 'Apps\CM_ElMoney\Controller\Affiliate\Statistics',
])
    ->addComponentNames('block', [
        'elmoney.balance' => 'Apps\CM_ElMoney\Block\Balance',
        'elmoney.currency' => 'Apps\CM_ElMoney\Block\Currency',
        'elmoney.enough' => 'Apps\CM_ElMoney\Block\Enough',
    ]);

group('/elmoney/', function(){
    route('gateway/setting/save', 'elmoney.admincp.gateway.settings');
    route('setting/save', 'elmoney.admincp.settings');
    route('admincp/funds/add', 'elmoney.admincp.funds.add');

    if (CM_EL_MONEY_IS_ACTIVE) {
        route('', 'elmoney.index');
        route('purchase', 'elmoney.index');
        route('sendtofriend', 'elmoney.sendtofriend');
        route('funds/add', 'elmoney.funds.add');
        route('confirm', 'elmoney.confirm');
        route('withdraw', 'elmoney.withdraw');
        route('withdraw/history', 'elmoney.withdraw.history');
        route('affiliate/links', 'elmoney.affiliate.links');
        route('affiliate/statistics', 'elmoney.affiliate.statistics');
    }

});
