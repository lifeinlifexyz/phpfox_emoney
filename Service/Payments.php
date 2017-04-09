<?php
namespace Apps\CM_ElMoney\Service;

use Phpfox;
use Phpfox_Service;

class Payments extends Phpfox_Service
{
    protected $_sTable = 'elmoney_payments';
    protected $sTable;
    /**
     * @var \Apps\CM_ElMoney\Service\Settings
     */
    protected $oSettings;

    /**
     * @var \Phpfox_Parse_Input
     */
    protected $oParse;

    public function __construct()
    {
        $this->sTable = Phpfox::getT($this->_sTable);
        $this->oSettings = Phpfox::getService('elmoney.settings');
        $this->oParse = \Phpfox_Parse_Input::instance();
    }

    public function setStatus($iId, $sStatus)
    {
        return $this->database()->update(\Phpfox::getT($this->_sTable),
            ['`status`' => $sStatus], '`payment_id` = ' . (int) $iId);
    }

    public function add($aVal)
    {

    }

    public function addFundsRequest($iUserId, $iAmount, $sCurrencyId, $sComment)
    {
        $iID = $this->database()->insert($this->sTable, [
            'seller_id' => 1,
            'user_id' => $iUserId,
            'is_add_funds' => 1,
            'time_stamp' => PHPFOX_TIME,
            'comment' => $this->oParse->clean($sComment, 300),
            'amount' => Phpfox::getService('elmoney')->calculateTotal($iAmount),
            'currency_code' => $this->oParse->clean($sCurrencyId, 5),
            'item_name' => _p('Add funds'),
            'return_url' => \Phpfox_Url::instance()->makeUrl('elmoney'),
        ]);

        $this->database()->update($this->sTable, ['item_number' => 'elmoney|' . $iID], 'payment_id = ' . $iID);

        return $this->database()->select('*')->from($this->sTable)->where('payment_id = ' . $iID)->get();
    }

    public function delete($iId)
    {
        $iId = (int) $iId;
        return $this->database()->delete($this->sTable, 'payment_id = ' . $iId);
    }

    public function get($iId)
    {
        return $this->database()->select('*')->from($this->sTable)->where('payment_id = ' . $iId)->get();
    }
}