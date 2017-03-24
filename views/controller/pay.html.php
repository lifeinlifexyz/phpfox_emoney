<?php
?>
<br>
{if isset($iUserBalance)}
    <div class="info">
        <div class="info_left">{_p('Your current balance')}:</div>
        <div class="info_right">{$iUserBalance}</div>
    </div>
    
    <div class="info">
        <div class="info_left">{_p('Need')}:</div>
        <div class="info_right">{$iAmount}</div>
    </div>
    
    <div class="info">
        <div class="info_left">{_p('lacks')}:</div>
        <div class="info_right">{$iLacks}</div>
    </div>
{else}
<form action="{url link='elmoney.pay'}" method="post">
    <input type="hidden" name="elmoney_seller_id" value="{$aForms.elmoney_seller_id}">
    <input type="hidden" name="buyer_id" value="{$aForms.buyer_id}">
    <input type="hidden" name="item_name" value="{$aForms.item_name}"> 
    <input type="hidden" name="item_number" value="{$aForms.item_number}"> 
    <input type="hidden" name="currency_code" value="{$aForms.currency_code}"> 
    <input type="hidden" name="return" value="{$aForms.return}"> 
    <input type="hidden" name="amount" value="{$aForms.amount}">
    <div class="table form-group">
        <label for="comment" class="table_left">{_p('Comment')}:</label>
        <div class="table_right">
            <textarea name="comment" id="comment" class="form-control"></textarea>
        </div>
    </div>

    <div class="table form-group">
        <div class="table_right">
            <input type="submit" class="btn btn-primary" value="{_p('Confirm')}">
        </div>
    </div>

</form>
{/if}