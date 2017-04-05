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
<div class=error_message>{_p('You do not have enough money. Please add funds to your account')}</div>

<div class=info>
    <div class=info_left>{_p('Your current balance')}:</div>
    <div class=info_right>{$iUserBalance}</div>
</div>

<div class=info>
    <div class=info_left>{_p('Need')}:</div>
    <div class=info_right>{$iAmount}</div>
</div>

<div class=info>
    <div class=info_left>{_p('lacks')}:</div>
    <div class=info_right>{$iLacks}</div>
</div>

<div>
    <a href='{url link="elmoney.funds.add"}'>{_p('+ Add funds')}</a>
</div>
