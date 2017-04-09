<?php

namespace Apps\CM_ElMoney\Controller;

use Phpfox;
use Phpfox_Plugin;

class WithdrawHistory extends \Phpfox_Component
{

    public function process()
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('elmoney.can_withdraw', true);
        $oSettings =  Phpfox::getService('elmoney.settings');
        if (!$oSettings['withdraw']) {
            $this->url()->send('profile.elmoney');
        }

        sectionMenu(_p('Add funds'), url('/elmoney/funds/add'), ['css_class' => 'popup']);
        \Phpfox_Template::instance()->buildSectionMenu('profile.elmoney', Phpfox::getService('elmoney')->getSectionMenu());

        $this->search()->setCondition(' AND `wh`.`user_id` = ' . Phpfox::getUserId());

        $aSearchFields = [
            'type' => 'browse',
            'field' => 'wh.withdraw_id',
            'search_tool' => [
                'when_field' => 'time_stamp',
                'table_alias' => 'wh',
                'search' => [
                    'action' => $this->url()->makeUrl('elmoney.withdraw.history'),
                    'default_value' => _p('Comment'),
                    'name' => 'search',
                    'field' => ['`wh`.`comment`']
                ],
                'sort' => [
                    'latest' => ['wh.withdraw_id', _p('Latest')],
                ],
                'show' => [12, 15, 18, 21]
            ]
        ];
        /**
         * @var $oSearch \Phpfox_Search
         */
        $oSearch = \Phpfox::getLib('search')->set($aSearchFields);

        $aBrowseParams = [
            'module_id' => 'elmoney',
            'alias' => 'wh',
            'field' => 'withdraw_id',
            'table' => Phpfox::getT('elmoney_withdraw'),
            'hide_view' => []
        ];

        $oSearch->setContinueSearch(true);

        $oSearch->browse()->params($aBrowseParams)->execute();

        $this->template()
            ->setTitle(_p('Withdraw history'))
            ->setBreadCrumb(_p('Withdraw history'))
            ->assign([
            'aItems' => $oSearch->browse()->getRows(),
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