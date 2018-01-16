<?php
namespace Apps\CM_ElMoney;

use Core\App;
use Phpfox;
use Core\App\Install\Setting;

/**
 * Class Install
 * @author  Neil
 * @version 4.5.0
 * @package Apps\PHPfox_Core
 */
class Install extends App\App
{

    /**
     * @var array
     */
    private $_app_phrases = [];
    public $vendor = '<a href="//codemake.org" target="_blank">CodeMake.Org</a> - See all our products <a href="//store.phpfox.com/techie/u/ecodemaster" target=_new>HERE</a> - contact us at => support@codemake.org';
    public $store_id = '1822';

    /**
     *
     */
    protected function setId()
    {
        $this->id = 'CM_ElMoney';
    }

    /**
     * Set start and end support version of your App.
     * @example   $this->start_support_version = 4.2.0
     * @example   $this->end_support_version = 4.5.0
     * @see       list of our verson at PF.Base/install/include/installer.class.php ($_aVersions)
     * @important You DO NOT ALLOW to set current version of phpFox for start_support_version and end_support_version. We will reject of app if you use current version of phpFox for these variable. These variables help clients know their sites is work with your app or not.
     */
    protected function setSupportVersion()
    {
        $this->start_support_version = Phpfox::getVersion();
        $this->end_support_version = Phpfox::getVersion();
    }


    /**
     *
     */
    protected function setAlias()
    {
        $this->alias = 'elmoney';
    }

    /**
     *
     */
    protected function setName()
    {
        $this->name = 'El Money';
    }

    /**
     *
     */
    protected function setVersion()
    {
        $this->version = '1.0.3';
    }

    /**
     *
     */
    protected function setSettings()
    {

    }

    /**
     *
     */
    protected function setUserGroupSettings()
    {
        $this->user_group_settings = [
            "elmoney.can_affiliate" => [
                "info" => "Can participate in affiliate?",
                "type" => "input:radio",
                "value" => [
                    "1"  => "1",
                    "2"  => "1",
                    "3"  => "0",
                    "4"  => "1",
                    "5"  => "0"
                  ],
              "options" => [
                    "yes" => "Yes",
                    "no" => "No"
              ]
            ],
            "elmoney.can_withdraw" => [
                  "info" => "Can withdraw?",
                  "type" => "input:radio",
                  "value" => [
                        "1"  => "1",
                    "2"  => "1",
                    "3"  => "0",
                    "4"  => "1",
                    "5"  => "0"
                  ],
                  "options" => [
                        "yes" => "Yes",
                    "no" => "No"
                  ]
            ],
            "elmoney.can_send_to_friend" => [
                  "info" => "Can send to a friend?",
                  "type" => "input:radio",
                  "value" => [
                        "1"  => "1",
                    "2"  => "1",
                    "3"  => "0",
                    "4"  => "1",
                    "5"  => "0"
                  ],
                  "options" => [
                        "yes" => "Yes",
                    "no" => "No"
                  ]
            ]
        ];
    }

    /**
     *
     */
    protected function setComponent()
    {
        $this->component = [
            "block"  => [
            "balance"  => "",
              "currency"  => ""
            ],
            "controller"  => [
                "index"  => "elmoney.index"
            ]
        ];
    }

    /**
     *
     */
    protected function setComponentBlock()
    {
        $this->component_block = [
            "Balance"  => [
                  "type_id"  => "0",
                  "m_connection"  => "elmoney",
                  "component"  => "balance",
                  "location"  => "1",
                  "is_active"  => "1",
                  "ordering"  => "3"
                ],
            "Currency"  => [
                  "type_id"  => "0",
                  "m_connection"  => "elmoney",
                  "component"  => "currency",
                  "location"  => "1",
                  "is_active"  => "1",
                  "ordering"  => "4"
            ]       
        ];
    }

    /**
     *
     */
    protected function setPhrase()
    {
        $this->phrase = $this->_app_phrases;
    }

    /**
     *
     */
    protected function setOthers()
    {
        $this->_publisher = 'CodeMake IT';
        $this->_publisher_url = 'http://codemake.org/';
        $this->_apps_dir = 'CM_ElMoney';
        $this->admincp_route = '/admincp/elmoney/gateway/settings';
        $this->admincp_menu = [
            _p("Settings") => "elmoney.settings",
            _p("Gateway Settings") => "elmoney.gateway.settings",
            _p("Manage user balance") => "elmoney.funds.manage",
            _p("Withdraw") => "elmoney.withdraw",
        ];
        $this->icon = 'https://raw.githubusercontent.com/codemakeorg/logo/master/elmoney.png';
        $this->menu = [
            "name" => "El Money",
            "url" => "/elmoney",
            "icon" => "money"
        ];
    }
}