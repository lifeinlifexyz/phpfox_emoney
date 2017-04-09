{if isset($currencies)}
<select name="currency" id="currency" class="form-control">
    {foreach from=$currencies key=currencyCode item=currency}
        <option {if isset($currentCurrency) && $currentCurrency == $currencyCode}selected="selected"{/if} value="{$currencyCode}">{_p var=$currency.name}</option>
    {/foreach}
</select>
<br/>
<input onclick="$.ajaxCall('elmoney.changeCurrency', 'currency='+$('#currency option:selected').val()+'&update=true');" class="btn btn-primary" type="button" value="{_p('Change currency')}" />
{/if}
