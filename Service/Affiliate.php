<?php

namespace Apps\CM_ElMoney\Service;

class Affiliate extends \Phpfox_Service
{
    private $sUserCodeTable;
    /**
     * @var \Phpfox_Parse_Input
     */
    protected $oParser;

    public function __construct()
    {
        $this->sUserCodeTable = \Phpfox::getT('elmoney_user_affilate_codes');
        $this->oParser = \Phpfox_Parse_Input::instance();
    }

    public function getUserCode($sType, $iItemId, $iUserId = null)
    {
        if (is_null($iUserId)) {
            $iUserId = \Phpfox::getUserId();
        }

        $aUserCode = $this->database()
            ->select('*')
            ->from($this->sUserCodeTable)
            ->where([
                ' AND `type` = \'' . $this->oParser->clean($sType, 50) . '\'',
                ' AND `item_id` = ' . (int) $iItemId,
                ' AND `user_id` = ' .  (int) $iUserId,
            ])->get();

        if (!empty($aUserCode)) {
            return $aUserCode;
        }

        $aVal = [
            '`type`' => $this->oParser->clean($sType, 50),
            '`item_id`' => (int) $iUserId,
            '`user_id`' => $iUserId,
            '`code`' => $this->generateCode(),
        ];

        $this->database()->insert($this->sUserCodeTable, $aVal);
        return $aVal;
    }


    public function getUserIDByCode($sCode)
    {
        $aCodes = $this->getAllCode();
        foreach($aCodes as $aCode) {
            if ($aCode['code'] == $sCode) {
                return $aCode['user_id'];
            }
        }
        return false;
    }

    protected function generateCode()
    {
        $sCode = uniqid();
        $aCodes = $this->getAllCode();
        foreach($aCodes as $aCode) {
            if ($aCode['code'] == $sCode) {
                $sCode = $this->getAllCode();
            }
        }
        return $sCode;
    }

    protected function getAllCode()
    {
        $sCashId = $this->cache()->set(['elmoney', 'affiliate', 'code']);

        if (empty(($aCodes = $this->cache()->get($sCashId)))) {
            $aCodes = $this->database()->select('*')->from($this->sUserCodeTable)->all();
        }

        return $aCodes;
    }

}