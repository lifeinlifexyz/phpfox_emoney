<?php
namespace Apps\CM_ElMoney\Service;

use Phpfox;
use Phpfox_Service;

class Trunsaction extends Phpfox_Service
{
    protected $_sTable = 'elmoney_trunsactions';
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
            'buyer_id' => (int) $aVal['buyer_id'],
            'seller_id' => (int) $aVal['elmoney_seller_id'],
            'status' => isset($aVal['status'])? $aVal['status'] : 'pending',
            'amount' => $this->oParse->clean($aVal['amount']),

            'cost' => isset($aVal['cost'])
                ? $aVal['cost']
                : Phpfox::getService('elmoney')->convertFrom($aVal['amount'], $aVal['currency_code']),

            'currency' => $this->oParse->clean($aVal['currency_code'], 5),
            'time_stamp' => PHPFOX_TIME,
            'item_name' => $this->oParse->clean($aVal['item_name'], 255),
            'item_number' => isset($aVal['item_number']) ?$this->oParse->clean($aVal['item_number'], 255) : '',
            'comment' => isset($aVal['comment']) ? $this->oParse->clean($aVal['comment'], 1000) : '',
            'is_add_funds' => isset($aVal['is_add_funds']) ? (int)$aVal['is_add_funds'] : 0,
            'buyer_balance' => isset($aVal['buyer_balance']) ? $this->oParse->clean($aVal['buyer_balance']) : 0,
            'seller_balance' => isset($aVal['buyer_balance']) ? $this->oParse->clean($aVal['seller_balance']) : 0,
        ]);
    }

    public function update($iTrId, $aVal)
    {
        return $this->database()->update($this->sTable, $aVal, '`transaction_id` = ' . (int) $iTrId);
    }

    public function delete($iId)
    {
        return $this->database()->delete($this->sTable, 'transaction_id = ' . (int) $iId);
    }

    public function get($iId)
    {
        return $this->database()->select('*')->from($this->sTable)->where('transaction_id = ' . (int)$iId)->get();
    }
}