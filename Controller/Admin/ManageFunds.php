<?php

namespace Apps\CM_ElMoney\Controller\Admin;

use Phpfox;
use Phpfox_Database;
use Phpfox_Locale;
use Phpfox_Pager;
use Phpfox_Plugin;
use Phpfox_Search;
use Phpfox_Url;
use User_Service_Browse;

class ManageFunds extends \Phpfox_Component
{
    public function process()
    {
        Phpfox::isAdmin(true);

        $aPages = [21, 31, 41, 51];
        $aDisplays = [];
        foreach ($aPages as $iPageCnt) {
            $aDisplays[$iPageCnt] = Phpfox::getPhrase('core.per_page', ['total' => $iPageCnt]);
        }

        $aSorts = [
            'u.full_name' => Phpfox::getPhrase('user.name'),
            'u.joined' => Phpfox::getPhrase('user.joined'),
            'u.last_login' => Phpfox::getPhrase('user.last_login'),
            'ufield.total_rating' => Phpfox::getPhrase('user.rating')
        ];

        $aAge = [];
        for ($i = Phpfox::getService('user')->age(Phpfox::getService('user')->buildAge(1, 1, Phpfox::getParam('user.date_of_birth_end'))); $i <= Phpfox::getService('user')->age(Phpfox::getService('user')->buildAge(1, 1, Phpfox::getParam('user.date_of_birth_start'))); $i++) {
            $aAge[$i] = $i;
        }

        $iYear = date('Y');
        $aUserGroups = [];
        foreach (Phpfox::getService('user.group')->get() as $aUserGroup) {
            $aUserGroups[$aUserGroup['user_group_id']] = Phpfox_Locale::instance()->convert($aUserGroup['title']);
        }

        $aGenders = Phpfox::getService('core')->getGenders();
        $aGenders[''] = (count($aGenders) == '2' ? Phpfox::getPhrase('user.both') : Phpfox::getPhrase('core.all'));

        $sDefaultOrderName = 'u.full_name';
        $sDefaultSort = 'ASC';
        if (Phpfox::getParam('user.user_browse_default_result') == 'last_login') {
            $sDefaultOrderName = 'u.last_login';
            $sDefaultSort = 'DESC';
        }
        $iDisplay = 12;

        $aFilters = [
            'display' => [
                'type' => 'select',
                'options' => $aDisplays,
                'default' => $iDisplay
            ],
            'sort' => [
                'type' => 'select',
                'options' => $aSorts,
                'default' => $sDefaultOrderName
            ],
            'sort_by' => [
                'type' => 'select',
                'options' => [
                    'DESC' => Phpfox::getPhrase('core.descending'),
                    'ASC' => Phpfox::getPhrase('core.ascending')
                ],
                'default' => $sDefaultSort
            ],
            'keyword' => [
                'type' => 'input:text',
                'size' => 15,
                'class' => 'txt_input'
            ],
            'type' => [
                'type' => 'select',
                'options' => [
                    '0' => [Phpfox::getPhrase('user.email_name'), 'AND ((u.full_name LIKE \'%[VALUE]%\' OR (u.email LIKE \'%[VALUE]@%\' OR u.email = \'[VALUE]\'))' . (defined('PHPFOX_IS_ADMIN_SEARCH') ? ' OR u.email LIKE \'%[VALUE]\'' : '') .')'],
                    '1' => [Phpfox::getPhrase('user.email'), 'AND ((u.email LIKE \'%[VALUE]@%\' OR u.email = \'[VALUE]\'' . (defined('PHPFOX_IS_ADMIN_SEARCH') ? ' OR u.email LIKE \'%[VALUE]%\'' : '') .'))'],
                    '2' => [Phpfox::getPhrase('user.name'), 'AND (u.full_name LIKE \'%[VALUE]%\')']
                ],
                'depend' => 'keyword'
            ],
            'group' => [
                'type' => 'select',
                'options' => $aUserGroups,
                'add_any' => true,
                'search' => 'AND u.user_group_id = \'[VALUE]\''
            ],
            'gender' => [
                'type' => 'input:radio',
                'options' => $aGenders,
                'default_view' => '',
                'search' => 'AND u.gender = \'[VALUE]\'',
                'suffix' => '<br />'
            ],
            'from' => [
                'type' => 'select',
                'options' => $aAge,
                'select_value' => Phpfox::getPhrase('user.from')
            ],
            'to' => [
                'type' => 'select',
                'options' => $aAge,
                'select_value' => Phpfox::getPhrase('user.to')
            ],
            'country' => [
                'type' => 'select',
                'options' => Phpfox::getService('core.country')->get(),
                'search' => 'AND u.country_iso = \'[VALUE]\'',
                'add_any' => true,
                // 'style' => 'width:150px;',
                'id' => 'country_iso'
            ],
            'country_child_id' => [
                'type' => 'select',
                'search' => 'AND ufield.country_child_id = \'[VALUE]\'',
                'clone' => true
            ],
            'status' => [
                'type' => 'select',
                'options' => [
                    '2' => Phpfox::getPhrase('user.all_members'),
                    '1' => Phpfox::getPhrase('user.featured_members'),
                    '4' => Phpfox::getPhrase('user.online'),
                    '3' => Phpfox::getPhrase('user.pending_verification_members'),
                    '5' => Phpfox::getPhrase('user.pending_approval'),
                    '6' => Phpfox::getPhrase('user.not_approved')
                ],
                'default_view' => '2',
            ],
            'city' => [
                'type' => 'input:text',
                'size' => 15,
                'search' => 'AND ufield.city_location LIKE \'%[VALUE]%\''
            ],
            'zip' => [
                'type' => 'input:text',
                'size' => 10,
                'search' => 'AND ufield.postal_code = \'[VALUE]\''
            ],
            'show' => [
                'type' => 'select',
                'options' => [
                    '1' => Phpfox::getPhrase('user.name_and_photo_only'),
                    '2' => Phpfox::getPhrase('user.name_photo_and_users_details')
                ],
                'default_view' => (Phpfox::getParam('user.user_browse_display_results_default') == 'name_photo_detail' ? '2' : '1')
            ],
            'ip' => [
                'type' => 'input:text',
                'size' => 10
            ]
        ];

        $aSearchParams = [
            'type' => 'browse',
            'filters' => $aFilters,
            'search' => 'keyword',
            'custom_search' => true
        ];


        $oFilter = Phpfox_Search::instance()->set($aSearchParams);

        $sStatus = $oFilter->get('status');
        $sView = $this->request()->get('view');
        $aCustomSearch = $oFilter->getCustom();
        $bIsOnline = false;
        $bPendingMail = false;
        $mFeatured = false;
        $bIsGender = false;

        switch ((int) $sStatus)
        {
            case 1:
                $mFeatured = true;
                break;
            case 3:
                $oFilter->setCondition('AND u.status_id = 1');
                break;
            case 4:
                $bIsOnline = true;
                break;
            case 5:
                $oFilter->setCondition('AND u.view_id = 1');
                break;
            case 6:
                $oFilter->setCondition('AND u.view_id = 2');
                break;
            default:

                break;
        }
        $aCallback = [
            'query' => true,
            'module' => 'elmoney',
        ];

        if (!empty($sView)) {
            switch ($sView) {
                case 'online':
                    $bIsOnline = true;
                    break;
                case 'featured':
                    $mFeatured = true;
                    break;
                case 'spam':
                    $oFilter->setCondition('u.total_spam > ' . (int) Phpfox::getParam('core.auto_deny_items'));
                    break;
                case 'pending':
                    $oFilter->setCondition('u.view_id = 1');
                    break;
                case 'top':
                    $bExtendContent = true;
                    $oFilter->setSort('ufield.total_rating');
                    $oFilter->setCondition('AND ufield.total_rating > ' . Phpfox::getParam('user.min_count_for_top_rating'));
                    if (($iUserGenderTop = $this->request()->getInt('topgender'))) {
                        $oFilter->setCondition('AND u.gender = ' . (int) $iUserGenderTop);
                    }

                    $iFilterCount = 0;
                    $aFilterMenuCache = [];

                    $aFilterMenu = [
                        Phpfox::getPhrase('user.all') => '',
                        Phpfox::getPhrase('user.male') => '1',
                        Phpfox::getPhrase('user.female') => '2'
                    ];

                    foreach ($aFilterMenu as $sMenuName => $sMenuLink) {
                        $iFilterCount++;
                        $aFilterMenuCache[] = [
                            'name' => $sMenuName,
                            'link' => $this->url()->makeUrl('user.browse', ['view' => 'top', 'topgender' => $sMenuLink]),
                            'active' => ($this->request()->get('topgender') == $sMenuLink ? true : false),
                            'last' => (count($aFilterMenu) === $iFilterCount ? true : false)
                        ];
                    }

                    $this->template()->assign([
                            'aFilterMenus' => $aFilterMenuCache
                        ]
                    );

                    break;
                default:

                    break;
            }
        }

        if (($iFrom = $oFilter->get('from')) || ($iFrom = $this->request()->getInt('from'))) {
            $oFilter->setCondition('AND u.birthday_search <= \'' . Phpfox::getLib('date')->mktime(0, 0, 0, 1, 1, $iYear - $iFrom). '\'' . ' AND ufield.dob_setting IN(0,1,2)');
            $bIsGender = true;
        }
        if (($iTo = $oFilter->get('to')) || ($iTo = $this->request()->getInt('to'))) {
            $oFilter->setCondition('AND u.birthday_search >= \'' . Phpfox::getLib('date')->mktime(0, 0, 0, 1, 1, $iYear - $iTo) .'\'' . ' AND ufield.dob_setting IN(0,1,2)');
            $bIsGender = true;
        }

        if (($sLocation = $this->request()->get('location'))) {
            $oFilter->setCondition('AND u.country_iso = \'' . Phpfox_Database::instance()->escape($sLocation) . '\'');
        }

        if (($sGender = $this->request()->getInt('gender'))) {
            $oFilter->setCondition('AND u.gender = \'' . Phpfox_Database::instance()->escape($sGender) . '\'');
        }

        if (($sLocationChild = $this->request()->getInt('state'))) {
            $oFilter->setCondition('AND ufield.country_child_id = \'' . Phpfox_Database::instance()->escape($sLocationChild) . '\'');
        }

        if (($sLocationCity = $this->request()->get('city-name'))) {
            $oFilter->setCondition('AND ufield.city_location = \'' . Phpfox_Database::instance()->escape(Phpfox::getLib('parse.input')->convert($sLocationCity)) . '\'');
        }

        $oFilter->setCondition('AND u.profile_page_id = 0');

        $bExtend = true;
        $iPage = $this->request()->getInt('page');
        $iPageSize = $oFilter->getDisplay();


        list($iCnt, $aUsers) = User_Service_Browse::instance()->conditions($oFilter->getConditions())
            ->callback($aCallback)
            ->sort($oFilter->getSort())
            ->page($oFilter->getPage())
            ->limit($iPageSize)
            ->online($bIsOnline)
            ->extend((isset($bExtendContent) ? true : $bExtend))
            ->featured($mFeatured)
            ->pending($bPendingMail)
            ->custom($aCustomSearch)
            ->gender($bIsGender)
            ->get();

        $iCnt = $oFilter->getSearchTotal($iCnt);
        $aNewCustomValues = [];
        if ($aCustomValues = $this->request()->get('custom')) {
            foreach ($aCustomValues as $iKey => $sCustomValue)
            {
                $aNewCustomValues['custom[' . $iKey . ']'] = $sCustomValue;
            }
        }

        Phpfox_Pager::instance()->set(array('page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt));

        Phpfox_Url::instance()->setParam('page', $iPage);

        if ($this->request()->get('featured') == 1) {
            $this->template()->setHeader([
                    'drag.js' => 'static_script',
                    '<script type="text/javascript">$Behavior.coreDragInit = function() { Core_drag.init({table: \'#js_drag_drop\', ajax: \'user.setFeaturedOrder\'}); }</script>'
                ]
            )
                ->assign(['bShowFeatured' => 1]);
        }
        foreach ($aUsers as $iKey => $aUser) {
            if (!isset($aUser['user_group_id']) || empty($aUser['user_group_id']) ||  $aUser['user_group_id'] < 1) {
                $aUser['user_group_id'] = $aUsers[$iKey]['user_group_id'] = 5;
                Phpfox::getService('user.process')->updateUserGroup($aUser['user_id'], 5);
                $aUsers[$iKey]['user_group_title'] = Phpfox::getPhrase('user.user_banned');
            }
            $aBanned = Phpfox::getService('ban')->isUserBanned($aUser);
            $aUsers[$iKey]['is_banned'] = $aBanned['is_banned'];
        }
        $aCustomFields = Phpfox::getService('custom')->getForPublic('user_profile');
        $this->template()
            ->setHeader('cache', [
                    'country.js' => 'module_core'
                ]
            )
            ->assign([
                    'aUsers' => $aUsers,
                    'bExtend' => $bExtend,
                    'aCallback' => $aCallback,
                    'bIsSearch' => $oFilter->isSearch(),
                    'bIsInSearchMode' => ($this->request()->getInt('search-id') ? true : false),
                    'aForms' => $aCustomSearch,
                    'aCustomFields' => $aCustomFields,
                    'sView' => $sView,
                ]
            );

        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('api.component_controller_admincp_elmoney_settings_clean')) ? eval($sPlugin) : false);
    }
}