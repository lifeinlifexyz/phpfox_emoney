<?php
if ($sMethod == 'getAffiliateCode') {
    return Phpfox::getService('elmoney.affiliate')->getUserCode(
        'subscribe', 1, _p('New member'), 'user.register',
        Phpfox::getService('elmoney.settings')->getAffiliatePercent('subscribe', 1));
}