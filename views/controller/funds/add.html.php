<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div id="gateways" style="display: none">
    <h3>{_p('Payment methods')}</h3>
    <div class="gateways"></div>
    <div class="separate"></div>
    <button id="add-fund-back" class="btn btn-primary">{_p('Back')}</button>
</div>
<div id="elmoney-add-funds">
    <form action="{url link='elmoney.funds.add'}"
          class="form-horizontal"
          data-commission='{$aCommission}'
          data-erate="{$iExchangeRate}"
          data-currency="{$sCurrency}"
    >
        <div class="form-group">
            <label class="col-sm-2 control-label">
                {_p('Amount')}:
            </label>
            <div class="col-sm-10">
                <input type="number" class="form-control" name="val[amount]" id="amount" value="{value type='input' id='amount'}" size="40" />
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
    {literal}
    <script type="text/javascript">
        $Behavior.elmoney_add_funds = function() {
            var form = $('#elmoney-add-funds');

            var aRawCommission = form.find('form').data('commission');
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

                return false;
            }

            $('#add-fund-back').off('click').on('click', function(){
                $.ajaxCall('elmoney.cancelAddFund', 'id='+ $("#gateways").data("id"));
                $('#gateways').hide();
                form.show();
            });

            form.find('#amount').off('change').on('change', function(){
                var summ = parseInt($(this).val());
                if (isNaN(summ) || summ == 0) {
                    $(this).closest('.form-group').addClass('has-error');
                    return false;
                }
                $(this).closest('.form-group').removeClass('has-error');

                var sComm = getCommission(summ);
                form.find('#commission').text(sComm);
                var iTotal =  +sComm +summ;
                form.find('#total').text(iTotal);
                form.find('#to-pay').text(form.find('form').data('currency') + (iTotal * form.find('form').data('erate')));
            });

            form.find('form').off('submit').on('submit', function(e) {
                e.preventDefault();
                var summ = parseInt($(this).find('#amount').val());
                if (isNaN(summ) || summ == 0) {
                    $(this).find('#amount').closest('.form-group').addClass('has-error');
                    return false;
                }
                sParams = $(this).serialize();
                $Core.ajaxMessage();
                $.ajaxCall('elmoney.addFunds', sParams + '&global_ajax_message=true');
                return false;
            });

        }
    </script>
    {/literal}
</div>

