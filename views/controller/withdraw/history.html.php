<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{if !PHPFOX_IS_AJAX}
<div class="cm-em-table-row cm-em-table-head">
    <div class="cm-em-table-cell">{_p('Gateway')}</div>
    <div class="cm-em-table-cell">{_p('Amount')}</div>
    <div class="cm-em-table-cell">{_p('Commission')}</div>
    <div class="cm-em-table-cell">{_p('Total')}</div>
    <div class="cm-em-table-cell">{_p('Withdraw')}</div>
    <div class="cm-em-table-cell">{_p('Status')}</div>
    <div class="cm-em-table-cell">{_p('Date')}</div>
    <div class="cm-em-table-cell">{_p('Comment')}</div>
</div>
{/if}
{foreach from=$aItems item=aItem}
<div class="cm-em-table-row">
    <div class="cm-em-table-cell">{$aItem.gateway}</div>
    <div class="cm-em-table-cell">{$aItem.amount|el_money_currency}</div>
    <div class="cm-em-table-cell">{$aItem.commission|el_money_currency}</div>
    <div class="cm-em-table-cell">{$aItem.total|el_money_currency}</div>
    <div class="cm-em-table-cell">{$aItem.currency|currency_symbol}{$aItem.withdraw|number_format:2}</div>
    <div class="cm-em-table-cell">{$aItem.status}</div>
    <div class="cm-em-table-cell">{$aItem.time_stamp|convert_time}</div>
    <div class="cm-em-table-cell">{$aItem.comment}</div>
</div>
{/foreach}
{pager}

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
