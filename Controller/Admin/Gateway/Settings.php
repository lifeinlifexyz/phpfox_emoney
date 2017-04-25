<?php

namespace Apps\CM_ElMoney\Controller\Admin\Gateway;


use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;

class Settings extends \Phpfox_Component
{
    public function process()
    {
        if (!($aGateway = Phpfox::getService('api.gateway')->getForEdit('elmoney')))
        {
            return Phpfox_Error::display(Phpfox::getPhrase('api.unable_to_find_the_payment_gateway'));
        }

        if (($aVals = $this->request()->getArray('val')))
        {
            if (Phpfox::getService('api.gateway.process')->update($aGateway['gateway_id'], $aVals))
            {
                \Phpfox::getService('admincp.module.process')->updateActivity('elmoney', $aVals['is_active']);
                db()->update(Phpfox::getT('menu'), ['is_active' => (int) $aVals['is_active']], '`url_value` = \'/elmoney\'');
                cache()->del('elmoney_gateway_setting');
                $this->url()->send('admincp.app',
                    [
                        'id' => 'CM_ElMoney',
                    ], Phpfox::getPhrase('api.gateway_successfully_updated'));
            }
        }

        $this->template()->setTitle(Phpfox::getPhrase('api.payment_gateways'))
            ->assign(array(
                    'aForms' => $aGateway
                )
            );
        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('api.component_controller_admincp_elmoney_gateway_settings_clean')) ? eval($sPlugin) : false);
    }
}