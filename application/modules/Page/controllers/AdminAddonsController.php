<?php

/**
 * Created by PhpStorm.
 * User: Медербек
 * Date: 07.09.2015
 * Time: 13:50
 */
class Page_AdminAddonsController extends Core_Controller_Action_Admin
{
    public function init()
    {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('page_admin_main', array(), 'page_admin_main_addons');
    }

    public function indexAction()
    {

    }
}