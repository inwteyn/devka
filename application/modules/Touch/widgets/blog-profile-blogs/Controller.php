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

class Touch_Widget_BlogProfileBlogsController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    $ynblog_enabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('ynblog');
    if((!$ynblog_enabled && !Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('blog')) || !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject();
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    // Just remove the title decorator
    $this->getElement()->removeDecorator('Title');

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $search = $request->getParam('search');
    $page = (int) $request->getParam('page');
    if($ynblog_enabled){
      $table = Engine_Api::_()->getDbTable('blogs', 'ynblog');
      $blogApi = Engine_Api::_()->getApi('core', 'ynblog');
      $blogsTbl = Engine_Api::_()->getDbTable('blogs', 'ynblog');
    } else {
    $table = Engine_Api::_()->getDbTable('blogs', 'blog');
    $blogApi = Engine_Api::_()->getApi('core', 'blog');
    $blogsTbl = Engine_Api::_()->getDbTable('blogs', 'blog');
    }

    $tableName = $table->info('name');
    if (method_exists($blogApi, 'getBlogsSelect')) {
      $select = $blogApi->getBlogsSelect(array(
        'orderby' => 'creation_date',
        'draft'  => '0',
        'user_id' =>  Engine_Api::_()->core()->getSubject()->getIdentity(),
      ));
    } else {
      $select = $blogsTbl->getBlogsSelect(array(
        'orderby' => 'creation_date',
        'draft'  => '0',
        'user_id' =>  Engine_Api::_()->core()->getSubject()->getIdentity(),
      ));
    }

    $this->view->form = $form = new Touch_Form_Search();
    if (!empty($search)){
      $form->getElement('search')->setValue($search);
      $select->where("$tableName.title LIKE ? OR $tableName.body LIKE ?", '%'.$search.'%');
    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(5);
    $paginator->setCurrentPageNumber($page);

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 && !$page && !$search ) {
      return $this->setNoRender();
    }

    // Add count to title if configured
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }

  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}