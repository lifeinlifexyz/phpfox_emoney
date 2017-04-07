<?php
?>
<form id="elmoney-send-to-friend"
      action="{url link='elmoney.sendtofriend'}"
      method="post"
      data-commission='{$aCommission}'>

    <div class="block">
        <div>{_p('Friends')}:</div>
        <div class="content">
            <div id="js_selected_friends" class="hide_it"></div>
            {module name='friend.search' friend_item_id=0 input='friends' hide=true friend_module_id='elmoney'}
        </div>

        <div class="form-group table">
            <div class="table_left">{_p('Amount')}:</div>
            <div class="table_right">
                <input id="amount" type="text" class="form-control" name="val[amount]" value="{value type='input' id='amount'}"/>
            </div>
        </div>

        <div class="form-group table">
            <div class="table_left">{_p('Commission')}:</div>
            <div class="table_right">
                <div class="form-control-static" id="commission">0</div>
            </div>
        </div>

        <div class="form-group table">
            <div class="table_left">{_p('Total')}:</div>
            <div class="table_right">
                <div class="form-control-static" id="total">0</div>
            </div>
        </div>

        <div class="form-group table">
            <div class="table_left">{_p('Comment')}:</div>
            <div class="table_right">
                <textarea name="val[comment]" class="form-control">{value type='textarea' id='comment'}</textarea>
            </div>
        </div>

        <div class="table_clear">
            <button type="submit" class="button btn btn-primary">{_p('Send')}</button>
        </div>
    </div>
</form>
{literal}
<script>
    $Behavior.cm_elmoney_sent_to_friend = function() {
        var form = $('#elmoney-send-to-friend');

        var aRawCommission = form.data('commission').send_to_friend;
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

        function displayCommission(summ) {
            var sComm = getCommission(summ);
            form.find('#commission').text(sComm);
            var iTotal =  +sComm +summ;
            if (!isNaN(iTotal)) {
                form.find('#total').text(iTotal);
            }
        }

        form.find('#amount').off('change').on('change', function(){
            var summ = parseInt($(this).val());
            if (isNaN(summ) || summ == 0) {
                $(this).closest('.form-group').addClass('has-error');
                return false;
            }
            $(this).closest('.form-group').removeClass('has-error');
            displayCommission(summ);
        });

        form.off('submit').on('submit', function(e) {
            var summ = parseInt($(this).find('#amount').val());
            if (isNaN(summ) || summ == 0) {
                $(this).find('#amount').closest('.form-group').addClass('has-error');
                e.preventDefault();
                return false;
            }
        });

        displayCommission(parseInt(form.find('#amount').val()));
    }
</script>
{/literal}

