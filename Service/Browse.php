<?php

namespace Apps\CM_ElMoney\Service;

use Phpfox;

class Browse extends \Phpfox_Service
{
    protected $_sTable = 'elmoney_trunsactions';
    /**
     * @var \Phpfox_Search
     */
    protected $oSearch;

    public function get($sUserField)
    {
        $iPage =  $this->oSearch->getPage();
        return $this->database()->select(Phpfox::getUserField() . ', `af`.`amount` as `affiliate_amount`, `tr`.*')
                ->from(Phpfox::getT($this->_sTable), 'tr')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ' . $sUserField)
                ->leftJoin(Phpfox::getT('elmoney_affiliate'), 'af', '`af`.`transaction_id` = `tr`.`transaction_id`')
                ->where($this->oSearch->getConditions())
                ->order($this->oSearch->getSort())
                ->limit($iPage, $this->oSearch->getDisplay(), null, false, false)
                ->execute('getSlaveRows');
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

    public function getQueryJoins()
    {

    }

    public function query(){}

}