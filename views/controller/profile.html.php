<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if count($aItems)}
{if !PHPFOX_IS_AJAX}
<div class="cm-table-row cm-table-head">
    <div class="cm-table-cell">{_p('Date')}</div>
    <div class="cm-table-cell">{_p('Amount')}</div>
    <div class="cm-table-cell">{_p('Balance')}</div>
</div>
{/if}
{foreach from=$aItems item=aItem}
<div class="cm-table-row">
    <div class="cm-table-cell">{$aItem.time_stamp|convert_time}</div>
    <div class="cm-table-cell">{$aItem.amount}</div>
    <div class="cm-table-cell">{$aItem.balance}</div>
</div>
{/foreach}
{pager}
{else}
<div class="extra_info">
    {_p('You do not have any payments')}
</div>
{/if}

