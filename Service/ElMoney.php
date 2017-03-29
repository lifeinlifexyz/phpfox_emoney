<?php
namespace Apps\CM_ElMoney\Service;

use Phpfox;
use Phpfox_Service;

class ElMoney extends Phpfox_Service
{

    private $aCommissions = [];
    /**
     * @var Settings
     */
    private $oSetting;
    private static $aCache = [];

    public function __construct()
    {
        $this->oSetting = Phpfox::getService('elmoney.settings');
    }

    public function isActive()
    {

        $aData = cache()->get('elmoney_gateway_setting');

        if (empty($aData)) {
            $aData = $this->database()->select('*')->from(Phpfox::getT('api_gateway'))->where('gateway_id = \'elmoney\'')->get();
            cache()->set('elmoney_gateway_setting', $aData);
        }

        return $aData['is_active'];
    }

    public function convertTo($iAmount, $sCurrency)
    {
        return $iAmount * $this->oSetting['exchange_rate_' . $sCurrency];
    }

    public function convertFrom($iAmount, $sCurrency)
    {
        return $iAmount / $this->oSetting['exchange_rate_' . $sCurrency];
    }

    public function calculateTotal($iSumm)
    {
        return $iSumm + $this->getCommission($iSumm);
    }

    public function getCommission($iAmount, $sType = 'add_funds')
    {
        $aComm = $this->getCommissions($sType);
        foreach($aComm as &$aItem) {
            if ($aItem['from'] <= $iAmount && $aItem['to'] >= $iAmount) {
                return $aItem['commission'];
            }
        }
        return 0;
    }

    public function getCommissions($sType = 'add_funds')
    {
        if (empty($this->aCommissions)) {
            $sCommissions = $this->oSetting['commissions'];
            $aRawCommission = json_decode($sCommissions, true);

            foreach($aRawCommission[$sType] as $sRawCommission) {
                $aComm = explode('|', $sRawCommission);
                $iComm = array_pop($aComm);
                $aComm = explode(':', $aComm[0]);
                $iFrom = $aComm[0];
                $iTo = $aComm[1];
                $this->aCommissions[] = [
                    'from' => $iFrom,
                    'to' => $iTo,
                    'commission' => $iComm,
                ];
            }
        }
        return $this->aCommissions;
    }

    public function setBalanceToUser($iUserId, $iBalance)
    {
        $sTable = Phpfox::getT('elmoney_user_balance');
        $aUserBalanse = $this->database()
            ->select('*')
            ->from($sTable)
            ->where('user_id = ' . $iUserId)
            ->get();

        if (empty($aUserBalanse)) {
            $this->database()->insert($sTable, ['balance' => $iBalance, 'user_id' => $iUserId]);
        } else {
            $this->database()->update($sTable, ['balance' => $iBalance], 'user_id = ' . $iUserId);
        }

        $sCacheId = $this->cache()->set(['elmoney_user_balance', $iUserId]);
        $this->cache()->remove($sCacheId);
        unset(self::$aCache[$iUserId]);
        return $this->getUserBalance($iUserId);
    }

    public function addBalanceToUser($iUserId, $iBalance)
    {
        $sCacheId = $this->cache()->set(['elmoney_user_balance', $iUserId]);
        $this->cache()->remove($sCacheId);
        unset(self::$aCache[$iUserId]);

        $sTable = Phpfox::getT('elmoney_user_balance');
        $aUserBalanse = $this->database()
            ->select('*')
            ->from($sTable)
            ->where('user_id = ' . $iUserId)
            ->get();

        if (empty($aUserBalanse)) {
            $this->database()->insert($sTable, ['balance' => $iBalance, 'user_id' => $iUserId]);
        } else {
            $this->database()->update($sTable, ['balance' => $aUserBalanse['balance'] + $iBalance], 'user_id = ' . $iUserId);
        }
        return $this->getUserBalance($iUserId);
    }

    public function reduceBalance($iUserId, $iBalance)
    {
        $iCurrBalance = $this->getUserBalance($iUserId);

        $sCacheId = $this->cache()->set(['elmoney_user_balance', $iUserId]);
        $this->cache()->remove($sCacheId);
        unset(self::$aCache[$iUserId]);

        $this->database()->update(Phpfox::getT('elmoney_user_balance'), [
            'balance' => $iCurrBalance - $iBalance,
        ],
            'user_id = ' . $iUserId);

        return $this->getUserBalance($iUserId);
    }

    public function getUserBalance($iUserId = null)
    {
        if ($iUserId === null) {
            if (!Phpfox::isUser()) {
                return false;
            }
            $iUserId = Phpfox::getUserId();
        }

        if (!isset(self::$aCache[$iUserId])) {
            $sCacheId = $this->cache()->set(['elmoney_user_balance', $iUserId]);
            $iBalance = $this->cache()->get($sCacheId);
            if (empty($iBalance)) {
                $iBalance = $this->database()
                    ->select('balance')
                    ->from(Phpfox::getT('elmoney_user_balance'))
                    ->where('user_id = ' . $iUserId)
                    ->limit(1)
                    ->execute('getfield');
                $this->cache()->save($sCacheId, $iBalance);
            }
            self::$aCache[$iUserId] = $iBalance;
        }
        return is_bool(self::$aCache[$iUserId]) ? 0 : self::$aCache[$iUserId];
    }

    public function currency($iAmount)
    {
        return $iAmount . ' ' . $this->oSetting['currency_code'];
    }

    public function getSectionMenu()
    {
        $aMenu =  [
            _p('Replenishment history') => '',
            _p('Purchase history') => 'purchase',
            _p('Sale history') => 'sold',
        ];

        if (Phpfox::isModule('friend')) {
            $aMenu[_p('Send to a friend')] = 'elmoney.sendtofriend';
        }

        return $aMenu;

    }
}