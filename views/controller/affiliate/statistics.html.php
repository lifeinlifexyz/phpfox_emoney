<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{if !PHPFOX_IS_AJAX}
<div class="cm-em-table-row cm-em-table-head">
    <div class="cm-em-table-cell">{_p('Title')}</div>
    <div class="cm-em-table-cell">{_p('Code')}</div>
    <div class="cm-em-table-cell">{_p('Amount')}</div>
    <div class="cm-em-table-cell">{_p('User')}</div>
    <div class="cm-em-table-cell">{_p('Date')}</div>
</div>
{/if}
{foreach from=$aItems item=aItem}
<div class="cm-em-table-row">
    <div class="cm-em-table-cell">{$aItem.title}</div>
    <div class="cm-em-table-cell">
        <input onclick="this.select();" type="text" readonly="readonly" value="{url link=$aItem.url elmoney_affiliate_code=$aItem.code}">
    </div>
    <div class="cm-em-table-cell">{$aItem.amount|el_money_currency}</div>
    <div class="cm-em-table-cell">{$aItem|user:'':'':50}</div>
    <div class="cm-em-table-cell">{$aItem.time_stamp|convert_time}</div>
</div>
{/foreach}
{literal}
<script>
    $Behavior.initCMTable = function() {
        $('.cm-em-table-row').closest('._block_content').css({
            display: 'table',
            width: '100%'
        });
    }

</script>
{/literal}
{pager}

