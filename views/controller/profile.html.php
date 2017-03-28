<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{if count($aItems)}
    {if $sView == 'purchase'}
        {template file='elmoney.controller.purchase-history'}
    {elseif ($sView == 'sold')}
        {template file='elmoney.controller.sold-history'}
    {else}
        {if !PHPFOX_IS_AJAX}
        <div class="cm-em-table-row cm-em-table-head">
            <div class="cm-em-table-cell">{_p('Amount')}</div>
            <div class="cm-em-table-cell">{_p('Date')}</div>
            <div class="cm-em-table-cell">{_p('status')}</div>
            <div class="cm-em-table-cell">{_p('Comment')}</div>
        </div>
        {/if}
        {foreach from=$aItems item=aItem}
        <div class="cm-em-table-row">
            <div class="cm-em-table-cell">{$aItem.amount|el_money_currency}</div>
            <div class="cm-em-table-cell">{$aItem.time_stamp|convert_time}</div>
            <div class="cm-em-table-cell">{$aItem.status}</div>
            <div class="cm-em-table-cell">{$aItem.comment}</div>
        </div>
        {/foreach}
        {pager}
    {/if}
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
{else}
    {if !PHPFOX_IS_AJAX}
        <div class="extra_info">
            {_p('You have not any items')}
        </div>
    {/if}
{/if}


