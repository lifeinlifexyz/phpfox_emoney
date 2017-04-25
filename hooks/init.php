<?php

if (isset($_GET['elmoney_affiliate_code'])) {
    Phpfox::getLib('session')->set('elmoney_affiliate_code', Phpfox_Parse_Input::instance()->clean($_GET['elmoney_affiliate_code']));
}