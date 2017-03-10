<?php

$oInstaller = new \Core\App\Installer();
$oInstaller->onInstall(function() use ($oInstaller){

    $oInstaller->db->query('CREATE TABLE IF NOT EXISTS `' . Phpfox::getT('elmoney_settings') . '` (
      `name` varchar(30) NOT NULL DEFAULT \'\',
      `value` text(100) NOT NULL DEFAULT \'\'
    );');

    if (!$oInstaller->db->select('count(*)')->from(Phpfox::getT('api_gateway'))->where('gateway_id = \'elmoney\'')->count()) {
        $oInstaller->db->insert(Phpfox::getT('api_gateway'), [
            'gateway_id' => 'elmoney',
            'title' => 'El Money',
            'description' => 'Some information about El Money...',
            'is_active' => '0',
            'is_test' => '0',
            'setting' => serialize([])
        ]);

    }

    copy(PHPFOX_DIR_SITE_APPS . 'CM_ElMoney' . PHPFOX_DS . 'gateway' . PHPFOX_DS . 'elmoney.class.php',
        PHPFOX_DIR_LIB_CORE . 'gateway' . PHPFOX_DS . 'api' . PHPFOX_DS  . 'elmoney.class.php');

});
