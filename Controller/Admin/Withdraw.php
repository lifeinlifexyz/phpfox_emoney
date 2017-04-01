<?php

namespace Apps\CM_ElMoney\Controller\Admin;

use Phpfox;
use Phpfox_Plugin;
use Phpfox_Search;

class Withdraw extends \Phpfox_Component
{

    public function process()
    {
        Phpfox::isAdmin(true);

        if ($this->request()->get('action') == 'cancel') {
            $iId = $this->request()->get('id');
            Phpfox::getService('elmoney.withdraw')->update($iId, ['status' => 'canceled']);
            $this->url()->send('admincp.app',
                [
                    'id' => 'CM_ElMoney',
                ], _p('Successfully canceled'));
        }

        $aSearchFields = [
            'type' => 'browse',
            'field' => 'wh.withdraw_id',
            'search_tool' => [
                'when_field' => 'time_stamp',
                'table_alias' => 'wh',
                'search' => [
                    'action' => $this->url()->makeUrl('admincp.elmoney.withdraw'),
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