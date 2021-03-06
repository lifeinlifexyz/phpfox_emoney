<?php

namespace Apps\CM_ElMoney\Service;

use Phpfox;

class Settings extends \Phpfox_Service implements \ArrayAccess
{
    protected $_sTable = 'elmoney_settings';
    protected $sTable;
    static $aSettings = [];
    private $sCacheId = '';

    public function __construct()
    {
        $this->sTable = Phpfox::getT($this->_sTable);
        $this->sCacheId = $this->cache()->set('elmoney_settings');
        self::$aSettings = $this->all();
    }

    public function save($aVals)
    {
        $this->cache()->remove($this->sCacheId);
        $this->_save($aVals);
        $this->cache()->remove($this->sCacheId);
        return $this;
    }

    private function _save($aVals)
    {
        foreach($aVals as $sName => &$sVal) {
            if (is_null($this->get($sName))) {
                $this->database()->insert($this->sTable, [
                    '`name`' => $sName,
                    '`value`' => $sVal,
                ]);
            } else {
                $this->database()->update($this->sTable, [
                    'value'=> $sVal,
                ], '`name` = \'' . $sName . '\'');
            }
        }
        return $this;
    }

    public function getAffiliatePercent($sType, $iItemId)
    {
        $sAffiliates = $this->get('affiliate');
        $aAffiliate = json_decode($sAffiliates, true);
        return isset($aAffiliate[$sType]['percent'][$iItemId]) ? $aAffiliate[$sType]['percent'][$iItemId] : 0;
    }

    public function saveAffiliatePercent($sTitle, $sType, $iItemId, $sValue)
    {
        $sAffiliates = $this->get('affiliate');
        $aAffiliate = json_decode($sAffiliates, true);
        $aAffiliate[$sType]['title'] = $sTitle;
        $aAffiliate[$sType]['percent'][$iItemId] = $sValue;
        $this['affiliate'] = json_encode($aAffiliate);
        return $this;
    }

    public function get($sName)
    {
        if (empty(self::$aSettings)) {
            self::$aSettings = $this->all();
        }
        return isset(self::$aSettings[$sName]) ? self::$aSettings[$sName] : null;
    }

    public function del($sName)
    {
        $this->database()->delete($this->sTable, '`name` = \'' . $sName . '\'');
        $this->cache()->remove($this->sCacheId);
        self::$aSettings = $this->all();
    }

    public function all()
    {
        if (empty($aSettings = $this->cache()->get($this->sCacheId))) {
            $aRawSettings = $this->database()->select('*')->from($this->sTable)->all();
            $aSettings = [];
            foreach($aRawSettings as $aRawSetting) {
                $aSettings[$aRawSetting['name']] = $aRawSetting['value'];
            }
            $this->cache()->save($this->sCacheId, $aSettings);
        }
        return $aSettings;
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset(self::$aSettings[$offset]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
       return $this->get($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->save([$offset => $value]);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        $this->del($offset);
    }
}