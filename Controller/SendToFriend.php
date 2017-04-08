<?php

namespace Apps\CM_ElMoney\Controller;

use Phpfox;
use Phpfox_Plugin;

class SendToFriend extends \Phpfox_Component
{

    public function process()
    {
        Phpfox::isUser(true);

        $oSettings =  Phpfox::getService('elmoney.settings');

        $this->template()->buildSectionMenu('profile.elmoney', Phpfox::getService('elmoney')->getSectionMenu());

        $aValidation = [
            'amount' => _p('Amount is required'),
            'friends' => _p('Friends is required'),
        ];

        $aVal = $this->request()->getArray('val');

        if ($_POST) {
            /**
             * @var $oValidator \Phpfox_Validator
             */
            $oValidator  = \Phpfox_Validator::instance()->set([
                'sFormName' => 'js_el_money',
                'aParams' => $aValidation,
            ]);

            if ($oValidator->isValid($aVal)) {
                $bIsValid = true;
                $iUserBalance = Phpfox::getService('elmoney')->getUserBalance();
                $iCommission = Phpfox::getService('elmoney')->getCommission($aVal['amount'], 'send_to_friend');
                $iTotalAmount = (count($aVal['friends']) * $aVal['amount']) + ($iCommission * count($aVal['friends']));

                if ($iUserBalance < $iTotalAmount) {
                    \Phpfox_Error::set(_p('You do not have enough money. Please add funds to your account'));
                    $bIsValid = false;
                }

                if ($bIsValid) {
                    $sUserCurrency = Phpfox::getService('user')->getCurrency();
                    foreach($aVal['friends'] as &$iFriendId) {
                        $iFriendBalance = Phpfox::getService('elmoney')->addBalanceToUser($iFriendId, $aVal['amount']);
                        $iUserBalance = Phpfox::getService('elmoney')->reduceBalance(Phpfox::getUserId(), $aVal['amount'] + $iCommission);
                        Phpfox::getService('elmoney.trunsaction')->add([
                            'buyer_id' => Phpfox::getUserId(),
                            'seller_id' => $iFriendId,
                            'status' => 'completed',
                            'amount' => $aVal['amount'],
                            'commission' => $iCommission,
                            'currency_code' => $sUserCurrency,
                            'item_name' => _p('Send to a friend'),
                            'comment' => $aVal['comment'],
                            'buyer_balance' => $iUserBalance,
                            'seller_balance' => $iFriendBalance,
                        ]);
                    }
                    $this->url()->send('profile.elmoney', ['view' => 'purchase'], _p('Successfully completed'));
                }
            }
        }

        $this->template()
            ->setTitle(_p('El Money'))
            ->setBreadCrumb(_p('El Money'))
            ->assign([
                'aSettings' => Phpfox::getService('elmoney.settings')->all(),
                'aCommission' => $oSettings['commissions'],
                'aForms' => $aVal,
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