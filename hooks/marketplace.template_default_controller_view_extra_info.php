<?php
$aListing = $this->getVar('aListing');
$iAffAllowed = Phpfox::getService('elmoney.settings')->getAffiliatePercent('marketplace', $aListing['listing_id']);

if ($aListing['user_id'] == Phpfox::getUserId() && $aListing['is_sell'] && $aListing['view_id'] != '2' &&  $aListing['price'] != '0.00') {
?>
    <div class="info" id="affiliate-block">
        <div class="info_left"><?=_p('Affiliate percent')?></div>
        <div class="info_right">
            <form onsubmit="$(this).ajaxCall('elmoney.affiliateEnable'); return false;" class="form-inline">
                <input type="hidden" name="type" value="marketplace">
                <input type="hidden" name="title" value="Marketplace">
                <input type="hidden" name="item_id" value="<?=$aListing['listing_id']?>">
                <div class="form-group">
                    <input class="form-control" type="text" name="percent" id="title_field" value="<?=$iAffAllowed?>" placeholder="<?=_p('percentage...')?>">
                </div>
                <button type="submit" class="button btn-primary"><?=_p('Save')?></button>
            </form>
        </div>
    </div>
<?php } elseif($iAffAllowed > 0 && $aListing['is_sell'] && $aListing['view_id'] != '2' && $aListing['price'] != '0.00') { ?>
    <div class="info" id="affiliate-block">
        <div class="info_left"><?=_p('Affiliate')?></div>
        <div class="info_right">
            <form onsubmit="$(this).ajaxCall('elmoney.affiliateLinkSave'); return false;" class="form-inline">
                <input type="hidden" name="type" value="marketplace">
                <input type="hidden" name="title" value="Marketplace: <?=$aListing['title']?>">
                <input type="hidden" name="url" value="marketplace.<?=$aListing['listing_id']?>">
                <input type="hidden" name="item_id" value="<?=$aListing['listing_id']?>">
                <button type="submit" class="button btn-primary"><?=_p('Get Affiliate link')?></button>
            </form>
        </div>
    </div>
<?php }?>
