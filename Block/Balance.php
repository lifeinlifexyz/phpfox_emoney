<?php

namespace Apps\CM_ElMoney\Block;

use Phpfox;
use Phpfox_Plugin;

class Balance extends \Phpfox_Component
{

    public function process()
    {
        Phpfox::isUser(true);
        $this->template()
            ->setTitle(_p('Add funds to your account'))
            ->setBreadCrumb(_p('Add funds to your account'))
            ->assign([
                'sHeader' => _p('Balance'),
                'iBalance' =>  Phpfox::getService('elmoney')->getUserBalance(),
            ]);
        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('api.component_block_balance_clean')) ? eval($sPlugin) : false);
    }
}