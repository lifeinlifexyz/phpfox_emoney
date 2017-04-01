<?php
namespace Apps\CM_ElMoney\Ajax;

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
}