<?php

namespace Apps\CM_ElMoney\Controller;

use Apps\CM_ElMoney\Service\ElMoney;
use Phpfox;
use Phpfox_Plugin;

class Confirm extends \Phpfox_Component
{

    public function process()
    {
        Phpfox::isUser(true);
        if (!($iTrId = $this->request()->getInt('id'))) {
            return \Phpfox_Error::display('Transaction not found', 404);
        }

        if (!($aVals = Phpfox::getService('elmoney.trunsaction')->get($iTrId))) {
            return \Phpfox_Error::display('Transaction not found', 404);
        }

        if ($aVals['buyer_id'] != Phpfox::getUserId()) {
            return \Phpfox_Error::display('You are not owner of this transaction', 404);
        }

        $aSectionMenu = Phpfox::getService('elmoney')->getSectionMenu();

        \Phpfox_Template::instance()->buildSectionMenu('profile.elmoney', $aSectionMenu);

        $iAmount = $aVals['amount'];
        $iUserBalance = Phpfox::getService('elmoney')->getUserBalance();

        if ($iUserBalance < $iAmount) {

            $this->template()->assign([
                'iUserBalance' => Phpfox::getService('elmoney')->currency($iUserBalance),
                'iAmount' => Phpfox::getService('elmoney')->currency($iAmount),
                'iLacks' => Phpfox::getService('elmoney')->currency($iAmount - $iUserBalance),
            ]);

            \Phpfox_Error::set(_p('You do not have enough money. Please add funds to your account'));

            sectionMenu(_p('Add funds'), url('/elmoney/funds/add'), ['css_class' => 'popup']);
            $this->template()->setTitle(_p('Purchase'))
                ->setBreadCrumb(_p('Purchase'), $this->url()->makeUrl('current'))
                ->setBreadCrumb(_p('Confirm'))
                ->assign('aForms', $aVals);
            return 'controller';
        }

        if (!isset($_POST['comment'])) {

            sectionMenu(_p('Add funds'), url('/elmoney/funds/add'), ['css_class' => 'popup']);
            $this->template()->setTitle(_p('Purchase'))
                ->setBreadCrumb(_p('Purchase'), $this->url()->makeUrl('current'))
                ->setBreadCrumb(_p('Confirm'))
                ->assign('aForms', $aVals);
            return 'controller';

        } else {

            Phpfox::getService('elmoney.trunsaction')
                ->update($iTrId, [
                    'comment' =>  \Phpfox_Parse_Input::instance()->clean($this->request()->get('comment', 1000)),
                    'status' => 'confirmed',
                ]);

            $aVals['elmoney_seller_id'] = $aVals['seller_id'];
            $aVals['currency_code'] = $aVals['currency'];
            $aVals['tr_id'] = $iTrId;

            $this->request()->set($aVals);
            \Api_Service_Gateway_Gateway::instance()->callback('elmoney');
            $this->url()->send($aVals['return']);

        }
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('api.component_controller_elmoney_confirm_clean')) ? eval($sPlugin) : false);
    }
}