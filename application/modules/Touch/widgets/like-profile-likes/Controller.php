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


class Touch_Widget_LikeProfileLikesController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
    if (!Engine_Api::_()->touch()->isModuleEnabled('like')) {
      $this->setNoRender();
      return ;
    }

    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if (Engine_Api::_()->core()->hasSubject()) {
      $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    } else {
      $this->view->subject = $subject = $viewer;
    }

    if ($subject->getType() != 'user') {
      $this->setNoRender();
      return ;
    }

    if (!$subject->authorization()->isAllowed($viewer, 'interest')) {
      $this->setNoRender();
      return ;
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $ipp = $settings->getSetting('like.profile_count', 9);

    $itemTypes = array_keys(Engine_Api::_()->like()->getSupportedModulesLabels());
    $table = Engine_Api::_()->getDbTable('likes', 'core');
    $select = $table->select()
      ->where('poster_type = ?', $subject->getType())
      ->where('poster_id = ?', $subject->getIdentity())
      ->where('resource_type IN ("'.implode('","', $itemTypes).'")');

    $page = $this->_getParam('page', 1);
    $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($page);
    $paginator->setItemCountPerPage($ipp);
    $items = array();

    foreach ($paginator as $data) {
      $item = Engine_Api::_()->getItem($data->resource_type, $data->resource_id);
      if (!$item){
        continue ;
      }
      $items[] = $item;
    }
    if ($this->getElement()->getTitle() && $page != 1) {
      $this->getElement()->setTitle('');
    }
    $this->view->this_total = $page*$ipp;
    $this->view->next_page = (isset($paginator->getPages()->next)) ? $paginator->getPages()->next : null;

    $this->view->paginator = $items;

    if( !empty($subject) ) {
      $this->view->subjectGuid = $subject->getGuid(false);
    }

    $this->view->total = $total = Engine_Api::_()->like()->getLikedCount($subject);

    if( $this->_getParam('titleCount', false) && $total > 0 ) {
      $this->_childCount = $total;
    }

    if (!$total || !$paginator->getTotalItemCount()) {
      $this->setNoRender();
      return ;
    }
  }
  
  public function getChildCount()
  {
    return $this->_childCount;
  }
}