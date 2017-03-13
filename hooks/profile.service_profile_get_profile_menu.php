<?php

if(CM_EL_MONEY_IS_ACTIVE && $aUser['user_id'] == Phpfox::getUserId()) {
    $aMenus[] = [
        'phrase' => _p('El Money'),
        'url' => 'profile.elmoney',
        'total' => Phpfox::getService('elmoney')->getUserBalance($aUser['user_id']),
        'icon' => 'feed/blog.png'
    ];
}