<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-02-20 15:35 Ulan T $
 * @author     Ulan T
 */

class Pagemusic_Widget_MusicsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if( !Engine_Api::_()->authorization()->isAllowed('music_playlist', null, 'view') ) {
      return $this->setNoRender();
    }

    // Get settings
    $settings = Engine_Api::_()->getApi('settings', 'core');

    // Get request
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();
    $params['ipp'] = $settings->getSetting('pagemusic.page', 10);

    //Get paginator
    $this->view->paginator = Engine_Api::_()->getApi('core', 'pagemusic')->getMusicPaginator($params);
  }
}
