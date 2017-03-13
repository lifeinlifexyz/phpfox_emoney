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

        $aAdminGateways = Phpfox::getService('api.gateway')->getActive();
        $aGateways = [];
        foreach ($aAdminGateways as &$aAdminGateway) {
            if ($aAdminGateway['gateway_id'] != 'elmoney') {
                $aGateways[$aAdminGateway['gateway_id']]['gateway'] = unserialize($aAdminGateway['setting']);
            }
        }

        $aPurchaseDetails = [
            'item_number' => 'elmoney|' . Phpfox::getUserId() . '_' . $iBalance,
            'currency_code' => $sUserCurrency,
            'amount' => $iPrice,
            'return' => \Phpfox_Url::instance()->makeUrl('profile.elmoney', ['payment' => 'done']),
            'item_name' => empty($aVal['comment']) ? _p('Add funds'): $aVal['comment'],
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
        $this->call('$("#gateways").show();');
    }


}