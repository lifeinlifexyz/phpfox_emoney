<?php
namespace Apps\CM_ElMoney\Block;

use Phpfox;
use Phpfox_Component;

class Currency extends Phpfox_Component
{
	public function process()
	{
//		d(Phpfox::getService('core.currency')->get()); die;
		$this->template()->assign([
			'sHeader' => _p('Your currency'),
			'currencies' => Phpfox::getService('core.currency')->get(),
			'currentCurrency' => Phpfox::getService('user')->getCurrency(),
		]);
		return 'block';
	}
}