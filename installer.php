<?php

$oInstaller = new \Core\App\Installer();
$oInstaller->onInstall(function() use ($oInstaller){

    $oInstaller->db->query('CREATE TABLE IF NOT EXISTS `' . Phpfox::getT('elmoney_settings') . '` (
      `name` varchar(30) NOT NULL DEFAULT \'\',
      `value` text(100) NOT NULL DEFAULT \'\'
    );');

    $oInstaller->db->query('CREATE TABLE IF NOT EXISTS `' . Phpfox::getT('elmoney_user_balance') . '` (
      `user_id` int(12) NOT NULL DEFAULT \'0\',
      `balance`  DECIMAL( 14, 2 )  NOT NULL DEFAULT \'0\',
      PRIMARY KEY (`user_id`)
    );');

    $oInstaller->db->query('CREATE TABLE IF NOT EXISTS `' . Phpfox::getT('elmoney_history') . '` (
      `history_id` int(11) NOT NULL AUTO_INCREMENT,
      `action` varchar(15) NOT NULL,
      `time_stamp` int(12) NOT NULL,
      `user_id` int(11) NOT NULL,
      `balance` int(11) NOT NULL,
      `product_name` varchar(255) NOT NULL,
      `amount` DECIMAL(14,2) NOT NULL DEFAULT  \'0.00\',
      `data` text NOT NULL,
      PRIMARY KEY (`history_id`),
      KEY `action` (`action`,`user_id`)
    )');

    $oInstaller->db->query('CREATE TABLE IF NOT EXISTS `' . Phpfox::getT('elmoney_payments') . '` (
      `payment_id` bigint(30) NOT NULL AUTO_INCREMENT,
      `seller_id` int(11) NOT NULL,
      `user_id` int(11) NOT NULL,
      `item_name` varchar(300) NOT NULL,
      `item_number` varchar(300) NOT NULL,
      `currency_code` varchar(5) NOT NULL,
      `return_url` varchar(200) NOT NULL,
      `amount` int(15) NOT NULL,
      `time_stamp` int(12) DEFAULT NULL,
      `status` varchar(15) NOT NULL DEFAULT \'pending\',
      `comment` varchar(300) NULL DEFAULT \'\',
      `is_add_funds` TINYINT( 1 ) NOT NULL DEFAULT  \'0\',
      PRIMARY KEY (`payment_id`),
      KEY `seller_id` (`seller_id`,`user_id`),
      KEY `status` (`status`)
    )');


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

    $aPhrase  = [
      'elmoney_user_exchange_rate_message' => 'The exchange rate of credits against the {user_currency}: 1 {elmoney_currency} = {exchange_rate}',
    ];

    $aLanguages = \Language_Service_Language::instance()->getAll();

    foreach($aPhrase as $sVar => $sText ) {
        $aText = [];
        foreach ($aLanguages as $aLanguage) {
            $aText[$aLanguage['language_id']] = $sText;
        }
        $aVal = [
            'product_id' => 'phpfox',
            'module' => 'elmoney|elmoney',
            'var_name' => $sVar,
            'text' => $aText
        ];
        \Language_Service_Phrase_Process::instance()->add($aVal);
    }

});
