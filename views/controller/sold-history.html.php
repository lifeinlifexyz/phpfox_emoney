<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{if !PHPFOX_IS_AJAX}
<div class="cm-em-table-row cm-em-table-head">
    <div class="cm-em-table-cell">{_p('Description')}</div>
    <div class="cm-em-table-cell">{_p('Amount')}</div>
    <div class="cm-em-table-cell">{_p('Commission')}</div>
    <div class="cm-em-table-cell">{_p('Buyer')}</div>
    <div class="cm-em-table-cell">{_p('Date')}</div>
    <div class="cm-em-table-cell">{_p('Status')}</div>
    <div class="cm-em-table-cell">{_p('Comment')}</div>
    <div class="cm-em-table-cell">{_p('Balance')}</div>
</div>
{/if}
{foreach from=$aItems item=aItem}
<div class="cm-em-table-row">
    <div class="cm-em-table-cell">{$aItem.item_name}</div>
    <div class="cm-em-table-cell">{$aItem.amount|el_money_currency}</div>
    <div class="cm-em-table-cell">
        {$aItem.commission|el_money_currency}
        {if $aItem.affiliate_amount > 0}
            <br>
            <span title="{_p('Affiliate')}">{$aItem.affiliate_amount|el_money_currency}</span>
        {/if}
    </div>
    <div class="cm-em-table-cell">{$aItem|user:'':'':50}</div>
    <div class="cm-em-table-cell">{$aItem.time_stamp|convert_time}</div>
    <div class="cm-em-table-cell">{$aItem.status}</div>
    <div class="cm-em-table-cell">{$aItem.comment}</div>
    <div class="cm-em-table-cell">{$aItem.seller_balance|el_money_currency}</div>

</div>
{/foreach}
{pager}
