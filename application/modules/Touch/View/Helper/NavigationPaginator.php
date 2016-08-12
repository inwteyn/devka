<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: NavigationPaginator.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

 
class Touch_View_Helper_NavigationPaginator extends Zend_View_Helper_Action
{
	public function navigationPaginator($navigation, $paginator, $params = array()){
		$data = array(
			'navigation'=>$navigation,
			'paginator'=>$paginator,
			'params'=>$params
		);

		return $this->view->partial(
      'navigation/paginator.tpl',
      'touch',
      $data
    );
	}
}