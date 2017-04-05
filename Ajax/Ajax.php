<?php
namespace Apps\CM_ElMoney\Ajax;

use Apps\CM_ElMoney\Service\ElMoney;
use Phpfox;
use Phpfox_Ajax;

class Ajax extends Phpfox_Ajax
{

    public function addFunds()
    {
        Phpfox::isUser(true);
        $sUserCurrency = Phpfox::getService('user')->getCurrency();
        $aVal = (array)$this->get('val');
        $iBalance = (int) $aVal['amount'];

        $iCommission = Phpfox::getService('elmoney')->getCommission($iBalance);
        $iPrice = Phpfox::getService('elmoney')->convertTo($iBalance + $iCommission, $sUserCurrency);

        $sItemName = 'Add funds to user account';
        $iTrId = Phpfox::getService('elmoney.trunsaction')->add([
            'is_add_funds' => true,
            'buyer_id' => Phpfox::getUserId(),
            'elmoney_seller_id' => 0,
            'amount' => $iBalance,
            'cost' => $iPrice,
            'item_name' => $sItemName,
            'comment' => $aVal['comment'],
            'currency_code' => $sUserCurrency,
        ]);

        $aItemNumber = 'elmoney|' . $iTrId;
        Phpfox::getService('elmoney.trunsaction')->update($iTrId, [
            'item_number' => $aItemNumber
        ]);

        $aAdminGateways = Phpfox::getService('api.gateway')->getActive();
        $aGateways = [];
        foreach ($aAdminGateways as &$aAdminGateway) {
            if ($aAdminGateway['gateway_id'] != 'elmoney') {
                $aGateways[$aAdminGateway['gateway_id']]['gateway'] = unserialize($aAdminGateway['setting']);
            }
        }

        $aPurchaseDetails = [
            'item_number' => $aItemNumber,
            'currency_code' => $sUserCurrency,
            'amount' => $iPrice,
            'return' => \Phpfox_Url::instance()->makeUrl('profile.elmoney', ['payment' => 'done']),
            'item_name' => $sItemName,
            'recurring' => '',
            'recurring_cost' => '',
            'alternative_cost' => '',
            'alternative_recurring_cost' => '',
            'fail_elmoney' => true,
        ];

        if (count($aGateways)) {
            foreach ($aGateways as $sGateway => $aData) {
                if (is_array($aData['gateway'])) {
                    foreach ($aData['gateway'] as $sKey => $mValue) {
                        $aPurchaseDetails['setting'][$sKey] = $mValue;
                    }
                } else {
                    $aPurchaseDetails['fail_' . $sGateway] = true;
                }
            }
        }
        Phpfox::getBlock('api.gateway.form', ['gateway_data' => $aPurchaseDetails]);

        $sContent = $this->getContent();
        $this->call('$("#elmoney-add-funds").hide();');
        $this->call('$("#gateways .gateways").html("' . $sContent . '");');
        $this->call('$("#gateways").data("id",' . $iTrId . ');');
        $this->call('$("#gateways").show();');
    }

    public function cancelAddFund()
    {
        Phpfox::isUser(true);
        $iId = $this->get('id');
        $aTrans = Phpfox::getService('elmoney.trunsaction')->get($iId);
        if ($aTrans['buyer_id'] == Phpfox::getUserId()) {
            Phpfox::getService('elmoney.trunsaction')->delete($iId);
        }
    }

    public function setBalance()
    {
        Phpfox::isAdmin(true);
        $iUserId = (int) $this->get('user_id');
        $iBalance = $this->get('balance');
        Phpfox::getService('elmoney')->setBalanceToUser($iUserId, $iBalance);
        $this->call('$("#user-' . $iUserId . ' .elmoney-balance *").removeAttr("disabled");');
        $this->call('$("#user-' . $iUserId . ' .elmoney-balance form").removeClass("disabled");');
        $this->call('$(".ajax_processing").remove();');
    }

    /**
     * @return object
     */
    public function changeCurrency()
    {
        Phpfox::isUser(true);
        if (!empty($this->get('update'))) {
            $sCurrency = $this->get('currency');
            Phpfox::getService('user.field.process')->update(Phpfox::getUserId(), 'default_currency', (empty($sCurrency) ? null :$sCurrency));
            cache()->del(['currency', Phpfox::getUserId()]);
        }
    }

    public function pay()
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
            'elmoney_seller_id' => (int)$this->get('elmoney_seller_id'),
            'buyer_id' => (int)$this->get('buyer_id'),
            'item_name' => $this->get('item_name'),
            'item_number' => $this->get('item_number'),
            'currency_code' => $this->get('currency_code'),
            'return' => $this->get('return'),
            'amount' => $this->get('amount'),
        ];


        if (!isset($_POST['comment'])) {
            /**
             * @var $oValidator \Phpfox_Validator
             */
            $oValidator  = \Phpfox_Validator::instance()->set([
                'sFormName' => 'js_cash_payment',
                'aParams' => $aValidation,
            ]);

            if ($oValidator->isValid($aVals)) {
                $bIsValid = true;

                if (Phpfox::getUserId() != ($iBuyerId = $aVals['buyer_id'])) {
                    $this->alert(_p('Buyer ID and your Id is not equal'));
                    return false;
                }

                $iAmount = $aVals['amount'];
                $iUserBalance = Phpfox::getService('elmoney')->getUserBalance();

                if ($iUserBalance < $iAmount) {
                    Phpfox::getBlock('elmoney.enough', [
                        'iUserBalance' => Phpfox::getService('elmoney')->currency($iUserBalance),
                        'iAmount' => Phpfox::getService('elmoney')->currency($iAmount),
                        'iLacks' => Phpfox::getService('elmoney')->currency($iAmount - $iUserBalance),
                    ]);
                    $this->html('form[action$="/elmoney/pay/"]', $this->getContent(false));
                    return false;
                }

                $aVals['commission'] = Phpfox::getService('elmoney')->getCommission($aVals['amount'], ElMoney::COMMISSION_SALE);
                $aVals['buyer_balance'] = Phpfox::getService('elmoney')->getUserBalance((int)$aVals['buyer_id']);
                $aVals['seller_balance'] = Phpfox::getService('elmoney')->getUserBalance((int)$aVals['elmoney_seller_id']);

                $iId = Phpfox::getService('elmoney.trunsaction')->add($aVals);
                Phpfox::getLib('session')->set('elmoney_tr', $iId);
                $this->call('$(\'form[action$="/elmoney/pay/"]\').siblings().remove();');

                $sContent = '<div class="table form-group">
                                <label for="comment" class="table_left">' . _p('Comment') . ':</label>
                                <div class="table_right">
                                    <textarea name="comment" id="comment" class="form-control"></textarea>
                                </div>
                              </div>

                                <div class="table form-group">
                                    <div class="table_right">
                                        <input type="submit" class="btn btn-primary" value="' . _p('Confirm') . '">
                                    </div>
                                </div>';

                $this->html('form[action$="/elmoney/pay/"]', $sContent);
                return true;



            } else {
                $this->alert(implode('\n\t', \Phpfox_Error::get()));
                return false;
            }


        } else {
            $iTrId = Phpfox::getLib('session')->get('elmoney_tr');
            if (empty($iTrId)) {
                $this->alert(_p('The session lifetime has expired'));
                return false;
            }
            $aTransaction = Phpfox::getService('elmoney.trunsaction')->get($iTrId);
            if ($aTransaction['buyer_id'] != Phpfox::getUserId()) {
                $this->alert(_p('You are not owner of this transaction!'));
                return false;
            }

            Phpfox::getService('elmoney.trunsaction')
                ->update($iTrId, [
                    'comment' =>  \Phpfox_Parse_Input::instance()->clean($this->get('comment', 1000)),
                    'status' => 'confirmed',
                ]);
            $aTransaction['tr_id'] = $iTrId;
            $aTransaction['currency_code'] = $aTransaction['currency'];
            $aTransaction['elmoney_seller_id'] = $aTransaction['seller_id'];
            request()->set($aTransaction);
            \Api_Service_Gateway_Gateway::instance()->callback('elmoney');
            Phpfox::getLib('session')->remove('elmoney_tr');

            $this->call('window.location.href="' . $aTransaction['return'] . '"');
            return true;
        }
    }
}