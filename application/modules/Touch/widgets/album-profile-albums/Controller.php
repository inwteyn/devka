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

class Touch_Widget_AlbumProfileAlbumsController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction(){
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject();
    if( !Engine_Api::_()->core()->hasSubject() || (!Engine_Api::_()->touch()->isModuleEnabled('album') && !Engine_Api::_()->touch()->isModuleEnabled('sitealbum')) || !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    // Just remove the title decorator
    $this->getElement()->removeDecorator('Title');

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $search = $request->getParam('search');
    $page = (int) $request->getParam('page');
    $table = Engine_Api::_()->getDbTable('albums', 'album');
    $tableName = $table->info('name');

    $albumApi = Engine_Api::_()->getApi('core', 'album');
    $albumsTbl = Engine_Api::_()->getDbTable('albums', 'album');

    if (method_exists($albumApi, 'getAlbumSelect')) {
      $select = $albumApi->getAlbumSelect(array('owner' => $subject, 'search' => 1));
    } else {
      $select = $albumsTbl->getAlbumSelect(array('owner' => $subject, 'search' => 1));
    }

    $this->view->form = $form = new Touch_Form_Search();
    if (!empty($search)){
      $form->getElement('search')->setValue($search);
      $select->where("$tableName.title LIKE ? OR $tableName.description LIKE ?", '%'.$search.'%');
    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($page);
    $paginator->setItemCountPerPage(5);

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 && !$page && !$search) {
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