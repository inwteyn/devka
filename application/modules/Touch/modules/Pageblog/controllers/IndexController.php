<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Pageblog_IndexController extends Touch_Controller_Action_Standard
{
    protected $params;
    protected $_subject;

    public function init()
    {
        $page_id = (int)$this->_getParam('page_id');
        $subject = null;
        $navigation = new Zend_Navigation();

        if ($page_id) {
            $subject = Engine_Api::_()->getDbTable('pages', 'page')->findRow($page_id);
        }

        if ($subject && !Engine_Api::_()->getApi('core', 'page')->isAllowedView($subject)) {
            $subject = null;
        }
        $viewer = Engine_Api::_()->user()->getViewer();

        if ($subject) {
            Engine_Api::_()->core()->setSubject($subject);
            $navigation->addPage(array(
                                      'label' => 'Browse Blogs',
                                      'route' => 'page_blog',
                                      'action' => 'index',
                                      'params' => array(
                                          'page_id' => $subject->getIdentity()
                                      )
                                 ));
            if ($subject->authorization()->isAllowed($viewer, 'posting')) {
                $navigation->addPage(array(
                                          'label' => 'Manage Blogs',
                                          'route' => 'page_blog',
                                          'action' => 'manage',
                                          'params' => array(
                                              'page_id' => $subject->getIdentity()
                                          )
                                     ));
            }
        }
        $this->_subject = $this->view->subject = $subject;
        $this->view->navigation = $navigation;
    }

    public function indexAction()
    {
        if (!$this->_subject) {
          return $this->_forward('index', 'index', 'page');
        }
    // Prepare data -=: By Ulan :=-
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('pageblog', null, 'create');
    $this->view->paginator = $this->getPaginator();
    }

    public function manageAction()
    {
        if (!$this->_subject) {
          return $this->_forward('index', 'index', 'page');
        }
      // Prepare data -=: By Ulan ask me if you have any questions ;):=-
      $user = Engine_Api::_()->user()->getViewer();
      $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('pageblog', null, 'create');
      $this->view->paginator = $this->getPaginator($user->getIdentity());
    }

    public function createAction()
    {
        if (!$this->_helper->requireUser->isValid())
            return;

        $this->view->page_id = $this->_getParam('page_id');
        $this->_subject = Engine_Api::_()->getItem('page', $this->view->page_id);
        
        $this->view->blog_id = $blog_id = $this->_getParam('blog_id');

        if( !Engine_Api::_()->core()->hasSubject() )
        {
            if ($blog_id !== null){
                    $subject = Engine_Api::_()->getItem('pageblog', $blog_id);
                    if( $subject && $subject->getIdentity() )
                    {
                      Engine_Api::_()->core()->setSubject($subject);
                    }
            }
        }


        $this->view->form = $form = new Touch_Form_PageBlog_Create();

        // Check method/data validitiy
        if( !$this->getRequest()->isPost() ) {
          return;
        }
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        $page_id = (int)$this->_getParam('page_id');
        $values = $this->getValues();
        $viewer = $this->_helper->api()->user()->getViewer();
        $values['user_id'] = $viewer->getIdentity();

        $db = Engine_Api::_()->getDbtable('pageblogs', 'pageblog')->getAdapter();
        $db->beginTransaction();
        

        try{
            $table = $this->_helper->api()->getDbtable('pageblogs', 'pageblog');
            $blog = $table->createRow();
            $blog->setFromArray($values);
            $blog->save();


                  // Add tags
            $tags = preg_split('/[,]+/', $values['tags']);
            $blog->tags()->addTagMaps($viewer, $tags);

             // Commit
            $db->commit();

        }catch(Exception $e){
            $db->rollBack();
            throw $e;
        }
        
        return $this->_forward('success', 'utility', 'touch', array(
          'messages' =>array($this->view->translate("TOUCH_BLOG_FORM_CREATE_SUCCESS") ),
          'parentRedirect' => $this->view->url(array('action' => 'view', 'page_id' => $page_id), 'page_view', true),
        ));

    }

    protected function getValues()
	{
		return array(
			'body' => trim($this->_getParam('blog_body')),
			'title' => trim($this->_getParam('blog_title')),
			'page_id' => (int)$this->_getParam('page_id'),
			'tags' => trim(Engine_String::strip_tags($this->_getParam('blog_tags'))),
			'blog_id' => (int)$this->_getParam('blog_id'),
		);
	}

    public function deleteAction()
    {
        $blog_id = (int)$this->_getParam('blog_id');

        $this->view->form = $form = new Touch_Form_Standard;

        $form->setTitle('Delete Blog')
                ->setDescription('Are you sure you want to delete this blog?')
                ->setAttrib('class', 'global_form_popup touchform')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setMethod('POST');

        $form->addElement('Button', 'submit', array(
                                                   'label' => 'Delete',
                                                   'type' => 'submit',
                                                   'ignore' => true,
                                                   'decorators' => array('ViewHelper')
                                              ));

        //    $form->addElement('Cancel', 'cancel', array(
        //      'label' => 'cancel',
        //      'link' => true,
        //      'prependText' => ' or ',
        //      'href' => urldecode($this->_getParam('return_url')),
        //      'decorators' => array(
        //        'ViewHelper'
        //      )
        //    ));
        //
        //    $form->addDisplayGroup(array('submit', 'cancel'), 'buttons');

        $form->setAction($this->view->url(array(
                                               'action' => 'delete',
                                               'blog_id' => $blog_id,
                                               'return_url' => $this->_getParam('return_url')
                                          ), 'page_blog'));

        if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $table = $this->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        $blog = $table->findRow($blog_id);
        $subject = $blog->getParent();
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$viewer->isSelf($blog->getOwner()) && !$subject->isTeamMember($viewer)) {
            return;
        }

        try
        {
            $search_api = Engine_Api::_()->getDbTable('search', 'page');
            $search_api->deleteData($blog);
            $blog->delete();
            $db->commit();
        }
        catch (Exception $e)
        {
            $db->rollBack();
            throw $e;
        }

        //    $return_url = $this->view->url(array(
        //      'action' => 'manage',
        //      'page_id' => $subject->getIdentity()
        //    ), 'page_blog', true);

        //    return $this->_forward('success', 'utility', 'touch', array(
        //      'messages' =>array($this->view->message),
        //      'parentRedirect' => $this->view->url(array('action' => 'manage'), 'page_browse', true),
        //    ));

      //  $this->_redirectCustom(array('route' => 'page_browse', 'action' => 'manage'));
      return $this->_forward('success', 'utility', 'touch', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'messages' =>array($this->view->message),
        'parentRedirect' => $this->view->url(array('controller'=>'index', 'action' => 'view', 'page_id' => $this->_getParam('page_id', null)), 'page_view', true)
      ));
    }

    public function viewAction()
    {
        $blog_id = (int)$this->_getParam('blog_id');
        $blog = $this->getTable()->findRow($blog_id);

        if (!$blog) {
          return $this->_forward('index', 'index', 'page');
        }

        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->subject = $blog->getParent();

        if ($viewer->getIdentity() != $blog->user_id) {
            $blog->view_count++;
            $blog->save();
        }

        $this->view->blog = $blog;
        $this->view->owner = $blog->getOwner();

    }

    protected function getApi()
    {
        return Engine_Api::_()->getApi('core', 'pageblog');
    }

    protected function getTable()
    {
        return Engine_Api::_()->getDbTable('pageblogs', 'pageblog');
    }
  protected function getPaginator($viewer_id = 0, $page = 1)
  {
    $table = $this->getTable();
    $this->view->form_filter = $form = new Touch_Form_Search();
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $this->view->form_value = $this->_getParam('search');
    }
    $select = $table->select()
        ->where('page_id = ?', $this->_subject->getIdentity());

    if ($viewer_id)
    {
      $select->where('user_id = ?', $viewer_id);
    }

    $select->order('modified_date DESC');
    if ($this->_getParam('search', false)) {
      $select->where('title LIKE ? OR body LIKE ?', '%' . $this->_getParam('search') . '%');
    }
    $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($page);
    $paginator->setItemCountPerPage(5);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    return $paginator;

  }
}