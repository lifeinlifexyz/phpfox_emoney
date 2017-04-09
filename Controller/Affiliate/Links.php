<?php

namespace Apps\CM_ElMoney\Controller\Affiliate;

use Phpfox;
use Phpfox_Plugin;

class Links extends \Phpfox_Component
{

    public function process()
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('elmoney.can_affiliate', true);

        sectionMenu(_p('Add funds'), url('/elmoney/funds/add'), ['css_class' => 'popup']);

        $aSectionMenu = Phpfox::getService('elmoney')->getSectionMenu();
        \Phpfox_Template::instance()->buildSectionMenu('profile.elmoney', $aSectionMenu);
        Phpfox::massCallback('getAffiliateCode');

        $aSupportedModules = Phpfox::getService('elmoney.affiliate')->getAllowedModules();
        foreach($aSupportedModules as $sModule) {
            Phpfox::callback($sModule . '.getAffiliateCode');
        }

        $aItems = Phpfox::getService('elmoney.affiliate')->getUserCodes(Phpfox::getUserId());

        $this->template()
            ->setTitle(_p('El Money'))
            ->setBreadCrumb(_p('Affiliate links'))
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