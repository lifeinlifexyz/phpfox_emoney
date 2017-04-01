<?php
?>
<form id="elmoney-withdraw-form"
      action="{url link='elmoney.withdraw'}" method="post"
      data-commission='{$aCommission}'
      data-erate="{$iExchangeRate}"
      data-currency="{$sCurrency}"
>

    <div class="table form-group">
        <div class="table_left">
            {_p('Select payment system')}:
        </div>
        <div class="table_right">
            {foreach from=$aGateways item=aGateway}
                <div class="radio">
                    <label>
                        <input type="radio" name="val[gateway]" value="{$aGateway.gateway_id}" />
                        {$aGateway.title}
                    </label>
                </div>
            {/foreach}
        </div>
    </div>

    <div class="table form-group">
        <div class="table_left">
            {_p('Amount')}:
        </div>
        <div class="table_right">
            <input type="text" class="form-control" name="val[amount]" id="amount" value="{value type='input' id='amount'}" size="40" />
            <div class="help-block">{$sUserExchangeRate}</div>
        </div>
    </div>
    <div class="table form-group">
        <div class="table_left">
            {_p('Commission')}:&nbsp;<span id="commission"></span>
        </div>
    </div>
    <div class="table form-group">
        <div class="table_left">
            {_p('total')}:&nbsp;<span id="total"></span>
        </div>
    </div>
    <div class="table form-group">
        <div class="table_left">
            {_p('Comment')}:
        </div>
        <div class="table_right">
            <textarea class="form-control" name="val[comment]">{value type='textarea' id='comment'}</textarea>
        </div>
    </div>
    <div class="table_clear">
        <input type="submit" class="btn btn-primary" value="{_p('Send')}">
    </div>
</form>

{literal}
<script type="text/javascript">
    $Behavior.elmoney_withdraw = function() {
        var form = $('#elmoney-withdraw-form');

        var aRawCommission = form.data('commission').withdraw;
        var aCommission = [];
        for(var i in aRawCommission) {
            var sRawCommission = aRawCommission[i];
            if (typeof(sRawCommission) == 'string') {
                var oCommssion = {
                    from:  sRawCommission.split('|')[0].split(':')[0],
                    to:  sRawCommission.split('|')[0].split(':')[1],
                    commission:  sRawCommission.split('|')[1]
                }
                aCommission.push(oCommssion);
            }
        }

        function getCommission(summ)
        {
            for(var i in aCommission) {
                var oComm = aCommission[i];
                if (oComm.from == undefined) {
                    continue;
                }
                if (summ >= oComm.from && summ <= oComm.to) {
                    return oComm.commission;
                }
            }

            return 0;
        }

        form.find('#amount').off('change').on('change', function(){
            var summ = parseInt($(this).val());
            if (isNaN(summ) || summ == 0) {
                $(this).closest('.form-group').addClass('has-error');
                return false;
            }
            $(this).closest('.form-group').removeClass('has-error');

            var sComm = getCommission(summ);
            form.find('#commission').text(sComm);
            var iTotal =  summ - sComm;
            var toWithdraw = iTotal / form.data('erate');
            form.find('#total').text(iTotal + ' = ' + form.data('currency') + toWithdraw );
        });

    }
</script>
{/literal}
