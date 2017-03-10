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

    <div class="table_clear">
        <input type="submit" value="{_p('Save')}" class="button btn-primary" />
    </div>
</form>

