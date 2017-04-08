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
        if (strpos($aParams['item_number'], 'wd') === false) {
            if (!($aInvoice = Phpfox::getService('elmoney.trunsaction')->get($aParams['item_number']))){
                Phpfox::log('Not a valid invoice');
                return false;
            }

            Phpfox::log('Purchase is valid: ' . var_export($aInvoice, true));

            $iUserId = $aInvoice['buyer_id'];
            $iBalance = $aInvoice['amount'];

            if ($aParams['status'] == 'completed') {

                if ($aParams['total_paid'] == $aInvoice['cost']) {
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
            $iBalance = Phpfox::getService('elmoney')->addBalanceToUser($iUserId, $iBalance);

            Phpfox::getService('elmoney.trunsaction')->update($aInvoice['transaction_id'], [
                'status' => 'completed',
                'buyer_balance' => $iBalance,
                'time_stamp' => PHPFOX_TIME,
            ]);
            Phpfox::log('Handling complete');
            return true;
        } else {
            Phpfox::log('Module callback recieved: ' . var_export($aParams, true));
            Phpfox::log('Attempting to retrieve purchase from the database');
            list(, $iId) = explode('_', $aParams['item_number']);
            if (!($aInvoice = Phpfox::getService('elmoney.withdraw')->get($iId))){
                Phpfox::log('Not a valid invoice');
                return false;
            }

            Phpfox::log('Purchase is valid: ' . var_export($aInvoice, true));

            $iUserId = $aInvoice['user_id'];
            $iBalance = $aInvoice['total'];

            if ($aParams['status'] == 'completed') {

                if ($aParams['total_paid'] == $aInvoice['withdraw']) {
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
            Phpfox::getService('elmoney')->reduceBalance($iUserId, $iBalance + $aInvoice['commission']);

            Phpfox::getService('elmoney.withdraw')->update($aInvoice['withdraw_id'], [
                'status' => 'completed',
                'time_stamp' => PHPFOX_TIME,
            ]);
            Phpfox::log('Handling complete');
            return true;
        }

    }

    public function onDeleteUser($iUser)
    {
        //todo:: delete user objects
//       $this->database()->delete(Phpfox::getT($this->_sTable), 'seller_id = ' . (int) $iUser);
    }

    /**
     * on users browse
     */
    public function getBrowseQueryCnt()
    {}

    /**
     * on users browse
     */
    public function getBrowseQuery()
    {
        $this->database()
            ->select('`elm_ub`.`balance` as user_el_balance, ')
            ->leftJoin(\Phpfox::getT('elmoney_user_balance'), '`elm_ub`', '`u`.`user_id` = `elm_ub`.`user_id`');
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