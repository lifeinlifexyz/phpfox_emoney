<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<form method="post" action="{url link='elmoney.setting.save'}">

    <div class="table form-group">
        <div class="table_left">
            {_p('Currency code')}:
        </div>
        <div class="table_right">
            <input type="text" class="form-control" name="val[currency_code]" id="currency_code" value="{value type='input' id='currency_code'}" size="40" />
        </div>
        <div class="clear"></div>
    </div>


    <div class="table form-group">
        <div class="table_left">
            {_p('Exchange rate of your currency')}:
        </div>
        <div class="table_right">
            {foreach from=$aCurrencies key=id item=aCurrency}
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon">{$aCurrency.symbol}</span>
                    <input type="text" class="form-control" name="val[{$aCurrency.var}]" id="{$aCurrency.var}" value="{value type='input' id=$aCurrency.var}" size="40" />
                </div>
            </div>
            {/foreach}
        </div>
        <div class="clear"></div>
    </div>

    <div class="table form-group">
        <div class="table_left">
            {_p('Commission of project(Fields format "from:to|Commission")')}:
        </div>
        <div class="table_right">
            <div id="commissions" data-phrase-remove="{_p('Remove')}">
                {foreach from=$aForms.commissions key=iKey item=sCommission}
                    <p class="commission-item">
                        <input type="text" class="form-control" name="val[commissions][]" value="{$sCommission}" size="40" placeholder="1:10|1"/>
                        {if $iKey != 0}
                            <a class="commission-item-del" href="#">{_p('Remove')}</a>
                        {/if}
                        <br>
                    </p>
                {/foreach}
            </div>
            <a id="add-new-field" class="settings_actions_link btn btn-success">
                <i class="fa fa-plus"></i>&nbsp;{_p('Add')}
            </a>
        </div>
        <div class="clear"></div>
    </div>

    <div class="table_clear">
        <input type="submit" value="{_p('Save')}" class="button btn-primary" />
    </div>
</form>
{literal}
<script type="text/javascript">
    $Behavior.initCommissionForm = function()
    {
        function remove(e)
        {
            e.preventDefault();
            $(e.target).closest('.commission-item').remove();
            return false;
        }

        var container = $('#commissions');

        $('#add-new-field').off('click').on('click', function() {
            var input = container.find('.commission-item:first-child').clone();

            if ($('.commission-item').length > 0) {
                var delLink = '<a class="commission-item-del" href="#">' + container.data('phraseRemove') + '</a>';
                $(delLink).insertAfter(input.find('input'));
            }
            input.appendTo('#commissions');

            $('.commission-item-del').off('click').on('click', function(e){
                remove(e);
            });
        });

        $('.commission-item-del').off('click').on('click', function(e){
            remove(e);
        });

    }
</script>
{/literal}

