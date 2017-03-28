<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div id="el-money-add-funds-page">
    {template file='elmoney.controller.admincp.user-filter'}

    <div class="block_content">
        {if $aUsers}
        <table cellpadding="0" cellspacing="0">
        <tr>
            <th>{phrase var='user.user_id'}</th>
            <th>{phrase var='user.photo'}</th>
            <th>{phrase var='user.display_name'}</th>
            <th>{phrase var='user.email_address'}</th>
            <th>{_p('Balance')}</th>
        </tr>
        {foreach from=$aUsers name=users key=iKey item=aUser}
        <tr id="user-{$aUser.user_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if} row{$iKey}" data-user-id="{$aUser.user_id}">
            <td>#{$aUser.user_id}</td>
            <td>{img user=$aUser suffix='_50_square' max_width=50 max_height=50}</td>
            <td>{$aUser|user}</td>
            <td><a href="mailto:{$aUser.email}">{if (isset($aUser.pendingMail) && $aUser.pendingMail != '')} {$aUser.pendingMail} {else} {$aUser.email} {/if}</a>{if isset($aUser.unverified) && $aUser.unverified > 0} <span class="js_verify_email_{$aUser.user_id}" onclick="$.ajaxCall('user.verifyEmail', 'iUser={$aUser.user_id}');">{phrase var='user.verify'}</span>{/if}</td>
            <td class="elmoney-balance">
                <form class="form-inline">
                    <div class="form-group">
                        <input type="text" class="elmoney-balance-value form-control" value="{$aUser.user_el_balance}">
                    </div>
                    <button class="btn btn-default" title="{_p('Save')}">
                        &nbsp;<i class="fa fa-save"></i>&nbsp;
                    </button>
                </form>
            </td>
        </tr>
        {/foreach}
        </table>
        {pager}
        {/if}
    </div>
    <div class="clearfix"></div>
</div>


{literal}
<script type="text/javascript">

    $Behavior.cm_elmoney_set_balance = function() {

        function balanceChange(e) {
            e.preventDefault();
            var target = $(e.target);
            if (target.hasClass('elmoney-balance-value') || target.hasClass('btn') || target.hasClass('fa-save')) {
                if (!(target.closest('.elmoney-balance').find('form').hasClass('disabled'))) {
                    target.closest('.elmoney-balance').find('form').addClass('disabled').find('*').attr('disabled', 'disabled');
                    target.closest('.elmoney-balance').trigger('elmoney.set_balance');
                }
            } else {

            }
            return false;
        }

        $('#el-money-add-funds-page .elmoney-balance form').off('submit').on('submit', balanceChange);
        $('#el-money-add-funds-page .elmoney-balance form input').off('change').on('change', balanceChange);
        $('#el-money-add-funds-page .elmoney-balance button').off('click').on('click', balanceChange);

        $('#el-money-add-funds-page .elmoney-balance').off('elmoney.set_balance').on('elmoney.set_balance', function(){
            var balance = $(this).find('input').val();
            var userId = $(this).closest('tr').data('userId');

            $Core.ajaxMessage();
            $.ajaxCall('elmoney.setBalance', 'user_id=' + userId + '&balance=' + balance + '&global_ajax_message=true');
        });

        $('#el-money-add-funds-page .elmoney-balance input').off('keydown').on('keydown', function(e){
            var TABKEY = 9;
            if(e.keyCode == TABKEY) {
                var next = $(this).closest('tr').next('tr');
                if (next != undefined && next.length > 0) {
                    $(this).closest('tr').next('tr').find('.elmoney-balance input').focus();
                } else {
                    $('.row0 .elmoney-balance input').focus();
                }
                return false;
            }
        });

    }

    $Behavior.cm_elmoney_users_filter = function() {

        $('#el-money-add-funds-page .pager  a').off('click').on('click', function(e) {
            e.preventDefault();
            $Core.processing();
            $.ajax({
                url: $(this).attr('href'),
                contentType: 'application/json',
                success: function(e) {
                    $('#el-money-add-funds-page').replaceWith(e.content).show();
                    $('.ajax_processing').remove();
                    $Core.loadInit();
                }
            });
            return false;
        });

        $('#cm-el-money-users-filter').submit(function(e){
            e.preventDefault();
            var form = this;
            $Core.processing();
            $.ajax({
                url: $(form).attr('action'),
                data: $(form).serialize(), // serializes the form's elements.
                contentType: 'application/json',
                success: function(data)
                {
                    $('#el-money-add-funds-page').replaceWith(data.content).show();
                    $('.ajax_processing').remove();
                    $Core.loadInit();
                }
            });
            return false;
        });
    }
</script>
{/literal}

