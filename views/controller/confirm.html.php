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
<form action="{url link='elmoney.confirm' id=$aForms.transaction_id}" method="post">
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