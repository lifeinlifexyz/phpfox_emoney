<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div id="gateways" style="display: none">
    <h3>{_p('Payment methods')}</h3>
    <div class="gateways"></div>
    <div class="separate"></div>
    <button id="add-fund-back" class="btn btn-primary" onclick="ElmoneyAddFunds.back(); return false;">{_p('Back')}</button>
</div>
<div id="elmoney-add-funds">
    <form action="{url link='elmoney.funds.add'}"
          class="form-horizontal"
          data-commission='{$aCommission}'
          data-erate="{$iExchangeRate}"
          data-currency="{$sCurrency}"
          onsubmit="ElmoneyAddFunds.send(this); return false;"
    >
        <div class="form-group">
            <label class="col-sm-2 control-label">
                {_p('Amount')}:
            </label>
            <div class="col-sm-10">
                <input type="number" onchange="ElmoneyAddFunds.changeAmount(this)" class="form-control" name="val[amount]" id="amount" value="{value type='input' id='amount'}" size="40" />
            </div>
            <div class="clear"></div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">
                &nbsp;
            </label>
            <div class="col-sm-10">
                <span class="form-control-static">{$sUserExchangeRate}</span>
            </div>
            <div class="clear"></div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">
                {_p('Commission')}:
            </label>
            <div class="col-sm-10">
                <div class="form-control-static" id="commission">0</div>
            </div>
            <div class="clear"></div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">
                {_p('Total')}:
            </label>
            <div class="col-sm-10">
                <div class="form-control-static" id="total">0</div>
            </div>
            <div class="clear"></div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">
                {_p('To pay')}:
            </label>
            <div class="col-sm-10">
                <div class="form-control-static" id="to-pay">0</div>
            </div>
            <div class="clear"></div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">
                {_p('Comment')}:
            </label>
            <div class="col-sm-10">
                <textarea class="form-control" name="val[comment]"></textarea>
            </div>
            <div class="clear"></div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">&nbsp;</label>
            <div class="col-sm-10">
                <input class="btn btn-primary" type="submit" value="{_p('Forward')}">
            </div>
            <div class="clear"></div>
        </div>
    </form>
</div>

{literal}
<script type="text/javascript">
    var ElmoneyAddFunds = {
        form:  $('#elmoney-add-funds'),
        aCommission: [],
        _defineCommission: function() {
            if (this.aCommission.length > 1) return;

            var aRawCommission = this.form.find('form').data('commission').add_funds;
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
            this.aCommission = aCommission;
        },
        getCommission: function(summ) {
            this._defineCommission();
            for(var i in this.aCommission) {
                var oComm = this.aCommission[i];
                if (oComm.from == undefined) {
                    continue;
                }
                if (summ >= oComm.from && summ <= oComm.to) {
                    return oComm.commission;
                }
            }

            return 0;
        },
        back: function() {
            $.ajaxCall('elmoney.cancelAddFund', 'id='+ $("#gateways").data("id"));
            $('#gateways').hide();
            this.form.show();
        },
        changeAmount: function(target) {
            var summ = parseInt($(target).val());
            if (isNaN(summ) || summ == 0) {
                $(target).closest('.form-group').addClass('has-error');
                return false;
            }
            $(target).closest('.form-group').removeClass('has-error');

            var sComm = this.getCommission(summ);
            this.form.find('#commission').text(sComm);
            var iTotal =  +sComm +summ;
            this.form.find('#total').text(iTotal);
            this.form.find('#to-pay').text(this.form.find('form').data('currency') + (iTotal * this.form.find('form').data('erate')));
        },
        send: function(target){
            var summ = parseInt($(target).find('#amount').val());
            if (isNaN(summ) || summ == 0) {
                $(target).find('#amount').closest('.form-group').addClass('has-error');
                return false;
            }
            var sParams = $(target).serialize();
            $Core.ajaxMessage();
            $.ajaxCall('elmoney.addFunds', sParams + '&global_ajax_message=true');
        }
    }
</script>
{/literal}

