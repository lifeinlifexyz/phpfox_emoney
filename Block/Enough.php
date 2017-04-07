<?php
/**
 * Created by PhpStorm.
 * User: bolotkalil
 * Date: 4/5/17
 * Time: 1:30 PM
 */

namespace Apps\CM_ElMoney\Block;


use Phpfox;

class Enough  extends \Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);

        $this->template()
            ->setTitle(_p('Add funds to your account'))
            ->setBreadCrumb(_p('Add funds to your account'))
            ->assign([
                'iUserBalance' => $this->getParam('iUserBalance'),
                'iAmount' => $this->getParam('iAmount'),
                'iLacks' => $this->getParam('iLacks'),
            ]);
        return 'block';
    }
}