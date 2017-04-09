<?php

namespace Apps\CM_ElMoney\Controller;

use Phpfox;
use Phpfox_Plugin;

class Index extends \Phpfox_Component
{

    public function process()
    {
        Phpfox::isUser(true);

        sectionMenu(_p('Add funds'), url('/elmoney/funds/add'), ['css_class' => 'popup']);
        $sView = $this->request()->get('view');

        $aSectionMenu = Phpfox::getService('elmoney')->getSectionMenu();

        \Phpfox_Template::instance()->buildSectionMenu('elmoney', $aSectionMenu);


        switch($sView) {
            case 'purchase':
                $this->search()->setCondition(' AND `tr`.`buyer_id` = ' . Phpfox::getUserId());
                $sTitle = _p('El Money');
                $sUserField = 'seller_id';
                break;
            case 'sold':
                $this->search()->setCondition(' AND `tr`.`seller_id` = ' . Phpfox::getUserId());
                $sUserField = 'buyer_id';
                $sTitle = _p('El Money');
                break;
            default:
                $this->search()->setCondition(' AND `tr`.`buyer_id` = ' . Phpfox::getUserId());
                $this->search()->setCondition(' AND `tr`.`is_add_funds` = \'1\'');
                $sTitle = _p('Replenishment history');
                $sUserField = 'buyer_id';

        }

        $aSearchFields = [
            'type' => 'browse',
            'field' => 'tr.transaction_id',
            'search_tool' => [
                'when_field' => 'time_stamp',
                'table_alias' => 'tr',
                'search' => [
                    'action' => $this->url()->makeUrl('elmoney', ['view' => 'purchase']),
                    'default_value' => _p('Comment'),
                    'name' => 'search',
                    'field' => ['`tr`.`item_name`', '`tr`.`item_number`', '`tr`.`comment`']
                ],
                'sort' => [
                    'latest' => ['tr.transaction_id', _p('Latest')],
                ],
                'show' => [12, 15, 18, 21]
            ]
        ];

        $oSearch = \Phpfox::getLib('search')->set($aSearchFields);

        $aItems =  Phpfox::getService('elmoney.browse')->setSearch($oSearch)->get($sUserField);
        $this->template()
            ->setTitle($sTitle)
            ->setBreadCrumb($sTitle)
            ->assign([
            'aItems' => $aItems,
            'sView' => $sView,
        ]);


    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('api.component_controller_elmoney_index_clean')) ? eval($sPlugin) : false);
    }
}