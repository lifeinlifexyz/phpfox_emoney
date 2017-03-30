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
            {_p('Commission for adding balance(Fields format "from:to|Commission")')}:
        </div>
        <div class="table_right">
            <div class="commissions" data-phrase-remove="{_p('Remove')}">
                {foreach from=$aForms.commissions.add_funds key=iKey item=sCommission}
                    <p class="commission-item">
                        <input type="text" class="form-control" name="val[commissions][add_funds][]" value="{$sCommission}" size="40" placeholder="1:10|1"/>
                        {if $iKey != 0}
                            <a class="commission-item-del" href="#">{_p('Remove')}</a>
                        {/if}
                        <br>
                    </p>
                {/foreach}
            </div>
            <a class="add-new-field settings_actions_link btn btn-success">
                <i class="fa fa-plus"></i>&nbsp;{_p('Add')}
            </a>
        </div>
        <div class="clear"></div>
    </div>

    <div class="table form-group">
        <div class="table_left">
            {_p('Commission for send to friend(Fields format "from:to|Commission")')}:
        </div>
        <div class="table_right">
            <div class="commissions" data-phrase-remove="{_p('Remove')}">
                {foreach from=$aForms.commissions.send_to_friend key=iKey item=sCommission}
                <p class="commission-item">
                    <input type="text" class="form-control" name="val[commissions][send_to_friend][]" value="{$sCommission}" size="40" placeholder="1:10|1"/>
                    {if $iKey != 0}
                    <a class="commission-item-del" href="#">{_p('Remove')}</a>
                    {/if}
                    <br>
                </p>
                {/foreach}
            </div>
            <a class="add-new-field settings_actions_link btn btn-success">
                <i class="fa fa-plus"></i>&nbsp;{_p('Add')}
            </a>
        </div>
        <div class="clear"></div>
    </div>

    <div class="table form-group">
        <div class="table_left">
            {_p('Withdrawal Commission(Fields format "from:to|Commission")')}:
        </div>
        <div class="table_right">
            <div class="commissions" data-phrase-remove="{_p('Remove')}">
                {foreach from=$aForms.commissions.withdraw key=iKey item=sCommission}
                <p class="commission-item">
                    <input type="text" class="form-control" name="val[commissions][withdraw][]" value="{$sCommission}" size="40" placeholder="1:10|1"/>
                    {if $iKey != 0}
                    <a class="commission-item-del" href="#">{_p('Remove')}</a>
                    {/if}
                    <br>
                </p>
                {/foreach}
            </div>
            <a class="add-new-field settings_actions_link btn btn-success">
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

        $('.add-new-field').off('click').on('click', function() {
            var container = $(this).closest('.table_right').find('.commissions');
            var input = container.find('.commission-item:first-child').clone();
            if (container.find('.commission-item').length > 0) {
                var delLink = '<a class="commission-item-del" href="#">' + container.data('phraseRemove') + '</a>';
                $(delLink).insertAfter(input.find('input'));
            }
            container.append(input);

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

