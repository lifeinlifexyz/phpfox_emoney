<?php

namespace Apps\CM_ElMoney\Service;

use Phpfox;

class Affiliate extends \Phpfox_Service
{
    private $sUserCodeTable;
    protected $_sTable = 'elmoney_affiliate';
    protected $sTable;
    /**
     * @var \Phpfox_Parse_Input
     */
    protected $oParser;

    /**
     * @var \Phpfox_Search
     */
    protected $oSearch;

    public function __construct()
    {
        $this->sUserCodeTable = \Phpfox::getT('elmoney_user_affilate_codes');
        $this->oParser = \Phpfox_Parse_Input::instance();
        $this->sTable = \Phpfox::getT($this->_sTable);
    }

    /**
     * @param \Phpfox_Search $oSearch
     * @return Browse
     */
    public function setSearch($oSearch)
    {
        $this->oSearch = $oSearch;
        return $this;
    }

    public function all()
    {
        $iPage =  $this->oSearch->getPage();
        return $this->database()->select(Phpfox::getUserField() . ', `af`.*, `al`.*')
            ->from($this->sTable, 'af')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = af.user_id')
            ->leftJoin($this->sUserCodeTable, 'al', '`al`.`code_id` = `af`.`code_id`')
            ->where($this->oSearch->getConditions())
            ->order($this->oSearch->getSort())
            ->limit($iPage, $this->oSearch->getDisplay(), null, false, false)
            ->execute('getSlaveRows');
    }

    public function add($aVals)
    {
        $aVal = [
            '`code_id`' => (int) $aVals['code_id'],
            '`user_id`' => (int) $aVals['user_id'],
            '`owner_id`' => (int) $aVals['owner_id'],
            '`amount`' => $aVals['amount'],
            '`transaction_id`' =>  (int) $aVals['transaction_id'],
            '`seller_id`' => (int) $aVals['seller_id'],
            '`time_stamp`' => PHPFOX_TIME,
        ];

        $this->database()->insert($this->sTable, $aVal);
    }

    public function getUserCodes($iUserId)
    {
        return $this->database()->select('*')->from($this->sUserCodeTable)->where('user_id = ' . (int) $iUserId)->all();
    }

    public function getUserCode($sType, $iItemId, $sTitle = '', $sUrl = '', $fPercent = 0.00, $iUserId = null)
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
            '`title`' => $this->oParser->clean($sTitle, 500),
            '`url`' => $this->oParser->clean($sUrl, 255),
            '`item_id`' => (int) $iItemId,
            '`user_id`' => (int) $iUserId,
            '`code`' => $this->generateCode(),
            '`percent`' => $fPercent,
        ];

        $this->database()->insert($this->sUserCodeTable, $aVal);
        $sCashId = $this->cache()->set(['elmoney', 'affiliate', 'code']);
        $this->cache()->remove($sCashId);
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

    public function getAffiliateByCode($sCode)
    {
        $aAffiliates = $this->getAllCode();
        foreach($aAffiliates as $aAffiliate) {
            if ($aAffiliate['code'] == $sCode) {
                return $aAffiliate;
            }
        }
        return false;
    }

    public function getAllowedModules()
    {
        return [
            'subscribe',
        ];
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