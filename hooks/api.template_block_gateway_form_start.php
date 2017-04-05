<?php
$aGataways = $this->getVar('aGateways');
$bElMoney = false;

foreach($aGataways as $aGataway) {
    if ($aGataway['gateway_id'] == 'elmoney') {
        $bElMoney = true;
    }
}

if ($bElMoney) {
    foreach($aGataways as $iKey => $aGataway) {
        if ($aGataway['gateway_id'] != 'elmoney') {
            unset($aGataways[$iKey]);
        }
    }
}
$this->assign('aGateways', $aGataways);
?>