<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="el_money_user_block_search">
    <form id="cm-el-money-users-filter" method="get" action="{url link='admincp.elmoney.funds.manage'}">
        <div class="table form-group">
            <div class="table_left">
                {phrase var='user.search'}:
            </div>
            <div class="table_right">
                {filter key='keyword'}
                <div class="extra_info">
                    {phrase var='user.within'}: {filter key='type'}
                </div>
            </div>
            <div class="clear"></div>
        </div>

        <div id="js_admincp_search_options" style="display:none;">

            <div class="table form-group">
                <div class="table_left">
                    {phrase var='user.user_group'}:
                </div>
                <div class="table_right">
                    {filter key='group'}
                </div>
                <div class="clear"></div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    {phrase var='user.gender'}:
                </div>
                <div class="table_right">
                    {filter key='gender'}
                </div>
                <div class="clear"></div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    {phrase var='user.location'}:
                </div>
                <div class="table_right">
                    {filter key='country'}
                    {module name='core.country-child' country_child_filter=true country_child_type='browse'}
                </div>
                <div class="clear"></div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    {phrase var='user.city'}:
                </div>
                <div class="table_right">
                    {filter key='city'}
                </div>
                <div class="clear"></div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    {phrase var='user.zip_postal_code'}:
                </div>
                <div class="table_right">
                    {filter key='zip'}
                </div>
                <div class="clear"></div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    {phrase var='user.ip_address'}:
                </div>
                <div class="table_right">
                    {filter key='ip'}
                </div>
                <div class="clear"></div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    {phrase var='user.age_group'}:
                </div>
                <div class="table_right">
                    {filter key='from'} {_p var="and"} {filter key='to'}
                </div>
                <div class="clear"></div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    {phrase var='user.show_members'}:
                </div>
                <div class="table_right">
                    {filter key='status'}
                </div>
                <div class="clear"></div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    {phrase var='user.sort_results_by'}:
                </div>
                <div class="table_right">
                    {filter key='sort'}
                </div>
                <div class="clear"></div>
            </div>

            <div class="table_header">
                {phrase var='user.custom_fields'}
            </div>
            {foreach from=$aCustomFields item=aCustomField}
            {template file='custom.block.foreachcustom'}
            {/foreach}
        </div>

        <div class="table_clear text-center">
            <div class="table_clear_more_options">
                <a href="#" rel="{phrase var='user.view_less_search_options'}" onclick="$('#js_admincp_search_options').toggle(); var text = $(this).text(); $(this).text($(this).attr('rel')); $(this).attr('rel', text); return false;">{phrase var='user.view_more_search_options'}</a>
            </div>
            <input type="submit" value="{phrase var='user.search'}" class="button btn-primary" name="search[submit]" />
        </div>
    </form>
</div>
