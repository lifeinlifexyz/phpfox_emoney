<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div id="withdraw-page">
    <div class="table_header">
        <div id="cm-filter-admin-block">
            {template file='elmoney.controller.admincp.filter'}
        </div>
        <h1>{_p('Withdraw')}</h1>
    </div>

    {if $aItems}
    {if !PHPFOX_IS_AJAX}
    <div class="cm-em-table-row cm-em-table-head">
        <div class="cm-em-table-cell">{_p('Gateway')}</div>
        <div class="cm-em-table-cell">{_p('Amount')}</div>
        <div class="cm-em-table-cell">{_p('Commission')}</div>
        <div class="cm-em-table-cell">{_p('Total')}</div>
        <div class="cm-em-table-cell">{_p('Withdraw')}</div>
        <div class="cm-em-table-cell">{_p('User')}</div>
        <div class="cm-em-table-cell">{_p('Status')}</div>
        <div class="cm-em-table-cell">{_p('Date')}</div>
        <div class="cm-em-table-cell">{_p('Comment')}</div>
        <div class="cm-em-table-cell">{_p('Action')}</div>
    </div>
    {/if}

    {foreach from=$aItems item=aItem}
    <div class="cm-em-table-row">
        <div class="cm-em-table-cell">{$aItem.gateway}</div>
        <div class="cm-em-table-cell">{$aItem.amount|el_money_currency}</div>
        <div class="cm-em-table-cell">{$aItem.commission|el_money_currency}</div>
        <div class="cm-em-table-cell">{$aItem.total|el_money_currency}</div>
        <div class="cm-em-table-cell">{$aItem.currency|currency_symbol}{$aItem.withdraw|number_format:2}</div>
        <div class="cm-em-table-cell">{$aItem|user:'':'':30}</div>
        <div class="cm-em-table-cell">{$aItem.status}</div>
        <div class="cm-em-table-cell">{$aItem.time_stamp|convert_time}</div>
        <div class="cm-em-table-cell">{$aItem.comment}</div>
        <div class="cm-em-table-cell">
            {if $aItem.status == 'pending'}
                <ul class="list-inline">
                    <li>
                        {module name='elmoney.admincp.withdraw' aData=$aItem}
                    </li>
                    <li>
                        <a href="{url link='admincp.elmoney.withdraw'}?action=cancel&id={$aItem.withdraw_id}" class="button btn-warning" title="{_p('Cancel')}">
                            <i class="fa fa-undo"></i>
                        </a>
                    </li>
                </ul>
            {/if}
        </div>
    </div>
    {/foreach}
    {pager}
    {/if}
</div>


{literal}
<script>
    $Behavior.cm_elmoney_withdraw_filter = function() {

        $('#cm-filter-admin-block a.ajax_link, #cm-filter-admin-block a.is_default').off('click').on('click', function(e) {
            e.preventDefault();
            $Core.processing();
            $.ajax({
                url: $(this).attr('href'),
                contentType: 'application/json',
                success: function(e) {
                    $('#withdraw-page').replaceWith(e.content).show();
                    $('.ajax_processing').remove();
                    $Core.loadInit();
                }
            });
            return false;
        });

        $('#cm-filter-admin-block form').submit(function(e){
            e.preventDefault();
            var form = this;
            $Core.processing();
            $.ajax({
                url: $(form).attr('action'),
                data: $(form).serialize(), // serializes the form's elements.
                contentType: 'application/json',
                success: function(data)
                {
                    $('#withdraw-page').replaceWith(data.content).show();
                    $('.ajax_processing').remove();
                    $Core.loadInit();
                }
            });
            return false;
        });
    }

</script>
{/literal}
