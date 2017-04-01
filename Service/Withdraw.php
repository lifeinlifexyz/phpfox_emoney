<?php
namespace Apps\CM_ElMoney\Service;

use Phpfox;
use Phpfox_Service;

class Withdraw extends Phpfox_Service
{
    protected $_sTable = 'elmoney_withdraw';
    protected $sTable;

    /**
     * @var \Phpfox_Parse_Input
     */
    protected $oParse;

    public function __construct()
    {
        $this->sTable = Phpfox::getT($this->_sTable);
        $this->oParse = \Phpfox_Parse_Input::instance();
    }

    public function add($aVal)
    {
        $iTotal = $aVal['amount'] - $aVal['commission'];

        return $this->database()->insert($this->sTable, [
            'gateway' => $this->oParse->clean($aVal['gateway']),
            'amount' => $this->oParse->clean($aVal['amount']),
            'comment' => $this->oParse->clean($aVal['comment'], 1000),
            'commission' => (int)$aVal['commission'],
            'total' => $iTotal,
            'withdraw' => Phpfox::getService('elmoney')->convertFrom($iTotal, $aVal['currency']),
            'currency' => $this->oParse->clean($aVal['currency'], 5),
            'time_stamp' => PHPFOX_TIME,
            'status' => 'pending',
            'user_id' => Phpfox::getUserId(),
        ]);
    }

    public function update($iTrId, $aVal)
    {
        return $this->database()->update($this->sTable, $aVal, '`withdraw_id` = ' . (int) $iTrId);
    }

    public function delete($iId)
    {
        return $this->database()->delete($this->sTable, 'withdraw_id = ' . (int) $iId);
    }

    public function get($iId)
    {
        return $this->database()->select('*')->from($this->sTable)->where('withdraw_id = ' . (int)$iId)->get();
    }
}