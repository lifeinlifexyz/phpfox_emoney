<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: form.html.php 7119 2014-02-18 13:55:48Z Fern $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if count($aGateways)}
{foreach from=$aGateways name=gateways item=aGateway}
<form method="post" action="{$aGateway.form.url}">
{foreach from=$aGateway.form.param key=sField item=sValue}
	<input type="hidden" name="{$sField}" value="{$sValue}" />
{/foreach}
	<button type="submit" title="{_p('Withdraw')}" class="button btn-primary btn-small">
		<i class="fa fa-money"></i>
	</button>
</form>
{/foreach}
{else}
<div class="extra_info">
	{phrase var='api.opps_no_payment_gateways_have_been_set_up_yet'}
</div>
{/if}
