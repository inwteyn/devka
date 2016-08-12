<?php

class Pagediscussion_Widget_ProfileDiscussionController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;

  public function indexAction() {

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('pagediscussion');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);

    $isPageEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page');
    $this->view->subject = $subject = (Engine_Api::_()->core()->hasSubject()) ? Engine_Api::_()->core()->getSubject('page') : false;

    if (!$isPageEnabled || !$subject) {
      return $this->setNoRender();
    }

    if (!in_array('pagediscussion', (array)$subject->getAllowedFeatures())) {
      return $this->setNoRender();
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    //$isTeamMember = $subject->isTeamMember($viewer);

    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

    $allowPost = Engine_Api::_()->getApi('core', 'pagediscussion')->isAllowedPost($subject);

    $this->view->page_id = $page_id = $subject->getIdentity();
    $this->view->canCreate = ($viewer->getIdentity() && $allowPost);

    $this->view->formCreate = new Pagediscussion_Form_Create();
    $this->view->formRename = new Pagediscussion_Form_Rename();
    $this->view->formPost = new Pagediscussion_Form_Post();
    $this->view->formEdit = new Pagediscussion_Form_Edit();

      $this->view->content_info = $content_info = $subject->getContentInfo();
      if (!empty($content_info['content'])){
        $this->view->init_js_str = $this->getApi()->getInitJs($content_info, $subject);
      }else{
        $this->view->init_js_str = "";
      }
      $p = 1;
    if($content_info['content'] == 'discussion_page') {
        if(!empty($content_info['content_id'])){
            $p = $content_info['content_id'];
        }
    }
    $ipp = $this->_getParam('itemCountPerPage', 10);
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('pagetopics', 'pagediscussion')
        ->getPaginator($page_id, $p, $ipp);
    $paginator->setCurrentPageNumber($p);

    $this->view->paginator = $paginator;
    $this->view->ipp = $ipp;

    $topic_list = array();
    foreach ($paginator as $item) {
      $topic_list[$item->getIdentity()] = $item->title;
    }
    $this->view->topic_list = $topic_list;

    if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }

  }

  public function getChildCount()
  {
    return $this->_childCount;
  }

  public function getApi()
  {
    return $this->api = Engine_Api::_()->getApi('core', 'pagediscussion');
  }

}