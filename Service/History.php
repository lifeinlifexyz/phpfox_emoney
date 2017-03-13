<?php
namespace Apps\CM_ElMoney\Service;

use Phpfox;
use Phpfox_Service;

class History extends Phpfox_Service
{
    protected $_sTable = 'elmoney_history';
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
        return $this->database()->insert($this->sTable, [
            'action' => $this->oParse->clean($aVal['action'], 30),
            'user_id' => (int) $aVal['user_id'],
            'balance' => (int) $aVal['balance'],
            'time_stamp' => PHPFOX_TIME,
            'product_name' => $this->oParse->clean('product', 255),
            'data' => json_encode($aVal),
        ]);
    }


    public function delete($iId)
    {
        $iId = (int) $iId;
        return $this->database()->delete($this->sTable, 'history_id = ' . $iId);
    }

    public function get($iId)
    {
        return $this->database()->select('*')->from($this->sTable)->where('history_id = ' . $iId)->get();
    }
}