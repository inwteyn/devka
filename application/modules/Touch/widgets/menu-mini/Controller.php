<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_Widget_MenuMiniController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    if(!Engine_Api::_()->getApi('settings', 'core')->core_general_search){
      if( $viewer->getIdentity()){
        $this->view->search_check = true;
      }
      else{
        $this->view->search_check = false;
      }
    }
    else $this->view->search_check = true;

		$this->view->navigation = $navigation = Engine_Api::_()
      ->getApi('menus', 'touch')
      ->getNavigation('core_mini');
		//$navigation->removePage();

    $count = (int)$this->_getParam('count', 3);
		$navigation_tmp = array();
		if ($navigation->count() > $count)
		{
			$i = $navigation->count();
			foreach($navigation as $nav)
			{
				$i--;
				if ($i >= $count)
				{
					$navigation_tmp[] = $nav;
				}
			}

			foreach($navigation_tmp as $nav_tmp)
			{
				$navigation->removePage($nav_tmp);
			}

			$this->view->more = $more = new Zend_Navigation_Page_Mvc(array(
				'label' => 'More+',
				'class' => 'menu_core_main core_mini_more',
				'visible' => 1,
				'action' => 'more-mini',
				'controller' => 'index',
				'module'=>'touch',
				'route' => 'default',
				'order' => '999'
			));
		}
  }
}