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

    $oInstaller->db->query('CREATE TABLE IF NOT EXISTS `' . Phpfox::getT('elmoney_trunsactions') . '` (
      `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
      `item_name` varchar(500) NOT NULL,
      `Item_number` varchar(300) NOT NULL,
      `cost` decimal(14,2) NOT NULL,
      `currency` varchar(5) NOT NULL,
      `type` varchar(10) NOT NULL,
      `status` varchar(20) NOT NULL,
      `buyer_id` int(11) NOT NULL,
      `seller_id` int(11) NOT NULL,
      `amount` decimal(14,2) NOT NULL,
      `comment` text NOT NULL,
      `buyer_balance` decimal(14,2) NOT NULL,
      `seller_balance` decimal(14,2) NOT NULL,
      `time_stamp` int(12) DEFAULT NULL,
      `is_add_funds` TINYINT( 1 ) NOT NULL DEFAULT  \'0\',
      `commission` DECIMAL( 14, 2 ) NOT NULL DEFAULT  \'0\',
      `return` VARCHAR( 500 ) NULL,
      PRIMARY KEY (`transaction_id`),
      KEY `is_add_funds` (`is_add_funds`),
      KEY `Item_number` (`item_number`,`buyer_id`,`seller_id`)
    )');

    $oInstaller->db->query('CREATE TABLE IF NOT EXISTS `' . Phpfox::getT('elmoney_withdraw') . '` (
      `withdraw_id` int(11) NOT NULL AUTO_INCREMENT,
      `gateway` varchar(100) NOT NULL,
      `amount` decimal(14,2) NOT NULL,
      `comment` text NOT NULL,
      `commission` int(11) NOT NULL,
      `total` decimal(14,2) NOT NULL,
      `withdraw` decimal(14,2) NOT NULL,
      `currency` varchar(5) NOT NULL,
      `time_stamp` int(12) NOT NULL,
      `status` varchar(15) NOT NULL,
      `user_id` int(11) NOT NULL,
      PRIMARY KEY (`withdraw_id`)
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
