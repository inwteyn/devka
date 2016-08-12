<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecomment
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     Bolot
 */
class Hecomment_Widget_CommentsfeedController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    // Get subject
    $subject = $this->_getParam('action', false);

    
    // Perms
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->canComment = $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
    $this->view->canDelete = $canDelete = $subject->authorization()->isAllowed($viewer, 'edit');
    
    // Likes
    $this->view->viewAllLikes = $this->_getParam('viewAllLikes', false);
    $this->view->likes = $likes = $subject->likes()->getLikePaginator();

    // Comments

    // If has a page, display oldest to newest
    if( null !== ( $page = $this->_getParam('page')) ) {
      $commentSelect = $subject->comments()->getCommentSelect();
      $commentSelect->order('comment_id ASC');
      $comments = Zend_Paginator::factory($commentSelect);
      $comments->setCurrentPageNumber($page);
      $comments->setItemCountPerPage(10);
      $this->view->comments = $comments;
      $this->view->page = $page;
    } else {
      // If not has a page, show the
      $commentSelect = $subject->comments()->getCommentSelect();
      $commentSelect->order('comment_id DESC');
      $comments = Zend_Paginator::factory($commentSelect);
      $comments->setCurrentPageNumber(1);
      $comments->setItemCountPerPage(4);
      $this->view->comments = $comments;
      $this->view->page = $page;
    }

    if( $viewer->getIdentity() && $canComment ) {
     $form = new Hecomment_Form_Comment();
     $reply_form = new Hecomment_Form_Reply();
      $form
        ->setIdentity($subject->getIdentity());

      $this->view->wallSmiles = Engine_Api::_()->getDbTable('smiles', 'wall')->getPaginator()->getCurrentItems();

      $this->view->form =$form;
      $this->view->subject =$subject;
      $this->view->reply_form =$reply_form;
     // $this->view->reply_form = $reply_form;
        //$form->setAction($this->view->url(array('action' => '')))
      $form->populate(array(
        'identity' => $subject->getIdentity(),
        'type' => $subject->getType(),
      ));
    }
    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('hecomment');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);
    // Hide if can't post and no comments
    if( !$canComment && !$canDelete && count($comments) <= 0 && count($likes) <= 0  ) {
      $this->setNoRender();
    }
  }
}