<?php

namespace Apps\CM_ElMoney\Controller\Affiliate;

use Phpfox;
use Phpfox_Plugin;

class Statistics extends \Phpfox_Component
{

    public function process()
    {
        Phpfox::isUser(true);

        sectionMenu(_p('Add funds'), url('/elmoney/funds/add'), ['css_class' => 'popup']);

        $aSectionMenu = Phpfox::getService('elmoney')->getSectionMenu();
        \Phpfox_Template::instance()->buildSectionMenu('profile.elmoney', $aSectionMenu);

        $this->search()->setCondition(' AND `af`.`owner_id` = ' . Phpfox::getUserId());

        $aSearchFields = [
            'type' => 'browse',
            'field' => 'af.affiliate_id',
            'search_tool' => [
                'when_field' => 'time_stamp',
                'table_alias' => 'af',
                'search' => [
                    'action' => $this->url()->makeUrl('elmoney.affiliate.statistics'),
                    'default_value' => _p('Amount'),
                    'name' => 'search',
                    'field' => ['`af`.`amount`']
                ],
                'sort' => [
                    'latest' => ['`af`.affiliate_id', _p('Latest')],
                ],
                'show' => [12, 15, 18, 21]
            ]
        ];

        $oSearch = \Phpfox::getLib('search')->set($aSearchFields);
        $aItems =  Phpfox::getService('elmoney.affiliate')->setSearch($oSearch)->all();

        $this->template()
            ->setTitle(_p('El Money'))
            ->setBreadCrumb(_p('Affiliate statistics'))
            ->assign([
            'aItems' => $aItems,
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