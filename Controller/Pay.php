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

        if (isset($_POST['comment'])) {
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

                $iTrId = Phpfox::getLib('session')->get('elmoney_tr');

                if (empty($iTrId)) {
                    \Phpfox_Error::set(_p('The session lifetime has expired'));
                    $bIsValid = false;
                }

                if ($bIsValid) {
                    Phpfox::getService('elmoney.trunsaction')
                        ->update($iTrId, [
                            'comment' =>  \Phpfox_Parse_Input::instance()->clean($this->request()->get('comment', 1000)),
                            'status' => 'confirmed',
                    ]);
                    $this->request()->set('tr_id', $iTrId);
//                    $this->request()->send(Phpfox::getLib('gateway')->url('elmoney'), $aVals);
                    \Api_Service_Gateway_Gateway::instance()->callback('elmoney');
                    $this->url()->send($aVals['return']);
                } else {
                    Phpfox::getService('elmoney.trunsaction')
                        ->update(Phpfox::getLib('session')->get('elmoney_tr'), [
                            'status' => 'invalid',
                        ]);
                }

            }
        } else {
            $iId = Phpfox::getService('elmoney.trunsaction')->add($aVals);
            Phpfox::getLib('session')->set('elmoney_tr', $iId);
        }

        $this->template()->setTitle(_p('Purchase'))
            ->setBreadCrumb(_p('Purchase'), $this->url()->makeUrl('current'))
            ->assign('aForms', $aVals);

        if (!isset($_POST['comment'])) {
            $this->template()->setBreadCrumb(_p('Confirm'));
        }
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