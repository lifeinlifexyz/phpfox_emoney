<?php

namespace Apps\CM_ElMoney\Controller;

use Phpfox;
use Phpfox_Plugin;

class Pay extends \Phpfox_Component
{

    public function process()
    {
        Phpfox::isUser(true);

        $aValidation =  [
            'elmoney_seller_id' => _p('Seller Id is required'),
            'buyer_id' => _p('Buyer Id is required'),
            'item_name' => _p('Item name is required'),
            'item_number' => _p('Item number is required'),
            'currency_code' => _p('Currency code is required'),
            'return' => _p('Return url is required'),
            'amount' => _p('Amount is required'),
        ];

        $aVals  =  [
            'elmoney_seller_id' => $this->request()->getInt('elmoney_seller_id'),
            'buyer_id' => $this->request()->getInt('buyer_id'),
            'item_name' => $this->request()->get('item_name'),
            'item_number' => $this->request()->get('item_number'),
            'currency_code' => $this->request()->get('currency_code'),
            'return' => $this->request()->get('return'),
            'amount' => $this->request()->get('amount'),
        ];
        /**
         * @var $oValidator \Phpfox_Validator
         */
        $oValidator  = \Phpfox_Validator::instance()->set([
            'sFormName' => 'js_cash_payment',
            'aParams' => $aValidation,
        ]);

        if ($oValidator->isValid($aVals)) {
            $bIsValid = true;
            if (Phpfox::getUserId() != ($iBuyerId = $this->request()->getInt('buyer_id'))) {
                \Phpfox_Error::set(_p('Buyer ID and your Id is not identical'));
                $bIsValid = false;
            }
            $iAmount = $this->request()->get('amount');
            $iUserBalance = Phpfox::getService('elmoney')->getUserBalance();

            if ($iUserBalance < $iAmount) {
                sectionMenu(_p('Add funds'), url('/elmoney/funds/add'), ['css_class' => 'popup']);
                $this->template()->assign([
                   'iUserBalance' => Phpfox::getService('elmoney')->currency($iUserBalance),
                    'iAmount' => Phpfox::getService('elmoney')->currency($iAmount),
                    'iLacks' => Phpfox::getService('elmoney')->currency($iAmount - $iUserBalance),
                ]);
                \Phpfox_Error::set(_p('You do not have enough money. Please add funds to your account'));
                $bIsValid = false;
            }
            if ($bIsValid) {
                $this->request()->send(Phpfox::getLib('gateway')->url('elmoney'), $aVals);
                $this->url()->send($aVals['return']);
            }

        }

        $this->template()->setTitle(_p('Purchase'))
            ->setBreadCrumb(_p('Purchase'));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('api.component_controller_elmoney_pay_clean')) ? eval($sPlugin) : false);
    }
}