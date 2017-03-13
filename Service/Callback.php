<?php
namespace Apps\CM_ElMoney\Service;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;

/**
 * Class Callback
 *
 * @package Apps\PHPfox_Groups\Service
 */
class Callback extends \Phpfox_Service
{

    public function getProfileLink()
    {
        return 'profile.elmoney';
    }

    public function paymentApiCallback($aParams)
    {
        Phpfox::log('Module callback recieved: ' . var_export($aParams, true));
        Phpfox::log('Attempting to retrieve purchase from the database');
        $aUserBalance = explode('_', $aParams['item_number']);

        if (count($aUserBalance) != 2) {
            Phpfox::log('Not a valid invoice');
            return false;
        }
        $iUserId = $aUserBalance[0];
        $iBalance = $aUserBalance[1];

        Phpfox::log('Purchase is valid: ' . var_export($aUserBalance, true));
        defined('PHPFOX_APP_USER_ID') || define('PHPFOX_APP_USER_ID', $iUserId);

        $sUserCurrency = Phpfox::getService('user')->getCurrency();

        if ($aParams['status'] == 'completed') {
            $iCommission = Phpfox::getService('elmoney')->getCommission($iBalance);
            $iPrice = Phpfox::getService('elmoney')->convertTo($iBalance + $iCommission, $sUserCurrency);

            if ($aParams['total_paid'] == $iPrice) {
                Phpfox::log('Paid correct price');
            } else {
                Phpfox::log('Paid incorrect price');

                return false;
            }
        } else {
            Phpfox::log('Payment is not marked as "completed".');

            return false;
        }

        Phpfox::log('Handling purchase');
        Phpfox::getService('elmoney')->addBalanceToUser($iUserId, $iBalance);
        $aVal = [
            'action' => 'add_funds',
            'user_id' => $iUserId,
            'product_name' => _p('Buy ' . Phpfox::getService('elmoney.settings')->get('currency_code')),
            'balance' => Phpfox::getService('elmoney')->getUserBalance($iUserId)
        ];
        Phpfox::log('Add history: ' . var_export($aVal, true));
        Phpfox::getService('elmoney.history')->add($aVal);
        Phpfox::log('Handling complete');
        return true;
    }

    public function onDeleteUser($iUser)
    {
        //todo:: delete user objects
//       $this->database()->delete(Phpfox::getT($this->_sTable), 'seller_id = ' . (int) $iUser);
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('elmoney.service_callback__call')) {
            eval($sPlugin);
            return;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}