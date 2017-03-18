<?php

namespace Apps\CM_ElMoney\Controller;

use Phpfox;
use Phpfox_Plugin;

class Profile extends \Phpfox_Component
{

    public function process()
    {
        Phpfox::isUser(true);

        sectionMenu(_p('Add funds'), url('/elmoney/funds/add'), ['css_class' => 'popup']);

        $aSearchFields = array(
            'type' => 'elmoney',
            'field' => 'eh.history_id',
            'search_tool' => [
                'table_alias' => 'eh',
                'search' => [
                    'action' => $this->url()->makeUrl('profile.elmoney'),
                    'default_value' => _p('Comment'),
                    'name' => 'search',
                    'field' => ['`eh`.`product_name`', '`eh`.`comment`']
                ],
                'sort' => [
                    'latest' => ['eh.history_id', _p('Latest')],
                ],
                'show' => [12, 15, 18, 21]
            ]
        );

        $this->search()->set($aSearchFields);
//
        $aBrowseParams = [
            'module_id' => 'elmoney',
            'alias' => 'eh',
            'field' => 'history_id',
            'table' => Phpfox::getT('elmoney_history'),
            'hide_view' => []
        ];

        $aSectionMenu = [
            _p('Replenishment') => 'profile.elmoney',
            _p('My Files') => 'digitaldownload.my',
            _p('Friends` Files') => 'digitaldownload.friends',
            _p('Invoices') => 'digitaldownload.invoice',
        ];

        \Phpfox_Template::instance()->buildSectionMenu('elmoney', $aSectionMenu);

        $this->search()->setCondition(' AND `eh`.`user_id` = ' . Phpfox::getUserId());

        switch($this->request()->get('reg3')) {
            default:
                $this->search()->setCondition(' AND `eh`.`action` = \'add_funds\'');

        }

        $this->search()->setContinueSearch(true);
        $this->search()->browse()->params($aBrowseParams)->execute();

        $this->template()
            ->setTitle(_p('Replenishment'))
            ->setBreadCrumb(_p('Replenishment'))
            ->assign([
            'aItems' => $this->search()->browse()->getRows()
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