<?php
$oDD = Phpfox::getService('digitaldownload.dd')->getDisplayer(request()->get('req2'));
$iAffAllowed = Phpfox::getService('elmoney.settings')->getAffiliatePercent('digitaldownload', $oDD['id']);
if ($oDD['user_id'] == Phpfox::getUserId()) {
?>
<div class="info" id="affiliate-block">
    <div class="info_left"><?=_p('Affiliate percent')?></div>
    <div class="info_right">
        <form onsubmit="$(this).ajaxCall('elmoney.affiliateEnable'); return false;" class="form-inline">
            <input type="hidden" name="type" value="digitaldownload">
            <input type="hidden" name="title" value="Digital Download">
            <input type="hidden" name="item_id" value="<?=$oDD['id']?>">
            <div class="form-group">
                <input class="form-control" type="text" name="percent" id="title_field" value="<?=$iAffAllowed?>" placeholder="<?=_p('percentage...')?>">
            </div>
            <button type="submit" class="button btn-primary"><?=_p('Save')?></button>
        </form>
    </div>
</div>
<?php } elseif($iAffAllowed > 0) { ?>
    <div class="info" id="affiliate-block">
        <div class="info_left"><?=_p('Affiliate')?></div>
        <div class="info_right">
            <form onsubmit="$(this).ajaxCall('elmoney.affiliateLinkSave'); return false;" class="form-inline">
                <input type="hidden" name="type" value="digitaldownload">
                <input type="hidden" name="title" value="Digital Download: <?=(string) $oDD?>">
                <input type="hidden" name="url" value="digitaldownload.<?=$oDD['id']?>">
                <input type="hidden" name="item_id" value="<?=$oDD['id']?>">
                <button type="submit" class="button btn-primary"><?=_p('Get Affiliate link')?></button>
            </form>
        </div>
    </div>
<?php }?>
