<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pageblog_IndexController extends Core_Controller_Action_Standard
{
	protected $params;
	protected $subject;
	
	public function init() 
	{
    if( isset($_GET['ul']) || isset($_FILES['Filedata']) ) return $this->_forward('upload-photo', null, null, array('format' => 'json'));
    if( isset($_GET['rp']) ) return $this->_forward('remove-photo', null, null, array('format' => 'json'));

		$path = Zend_Controller_Front::getInstance()->getControllerDirectory('pageblog');
    $path = dirname($path) . '/views/scripts';
    
  	$this->view->addScriptPath($path);
  	
  	$path = Zend_Controller_Front::getInstance()->getControllerDirectory('like');
    $path = dirname($path) . '/views/scripts';
    
  	$this->view->addScriptPath($path);
  	
		$this->view->page_id = $this->_getParam('page_id');
		
		$this->view->subject = $this->subject = Engine_Api::_()->getItem('page', $this->view->page_id);
		$this->view->isAllowedView = $this->getPageApi()->isAllowedView($this->subject);
		
		if (!$this->view->isAllowedView){
			$this->view->isAllowedPost = false;
			$this->view->isAllowedComment = false;
			return ;
		}
		
		$this->view->isAllowedPost = $this->getApi()->isAllowedPost($this->subject);
		$this->view->isAllowedComment = $this->getPageApi()->isAllowedComment($this->subject);

		$this->view->blog_id = $blog_id = $this->_getParam('blog_id');
		
		if( !Engine_Api::_()->core()->hasSubject() ) {
    	if ($blog_id !== null){
				$subject = Engine_Api::_()->getItem('pageblog', $blog_id);
				if( $subject && $subject->getIdentity() ) {
				  Engine_Api::_()->core()->setSubject($subject);
				}
    	}
    }
    
		$this->params = array('page_id' => $this->view->page_id, 'ipp' => $this->_getParam('ipp', 10), 'p' => $this->_getParam('p', 1));
	}
	
	public function indexAction() 
	{
		$table = $this->getTable();
		$this->view->blogs = $table->getBlogs($this->params);
		if (!$this->subject->isTeamMember()){
			$this->view->html = $this->view->render('list.tpl');
		}else{
			$this->view->html = $this->view->render('list_edit.tpl');
		}
	}
	
	public function mineAction() 
	{
		$table = $this->getTable();
		$this->params['user_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
		$this->view->blogs = $table->getBlogs($this->params);
		
		$this->view->html = $this->view->render('list_edit.tpl');
	}
	
	public function deleteAction()
	{
		$this->view->eval = "self.my_blogs();";
		if ($this->view->isAllowedPost){
			$where = "page_id = {$this->view->page_id} AND pageblog_id = {$this->view->blog_id}";
			
			$table = $this->getTable();
			$blog = $table->fetchRow($table->select()->where($where));

			$search_api = Engine_Api::_()->getDbTable('search', 'page');
			$search_api->deleteData($blog);

			$blog->delete();
			
			$this->view->eval = "self.inc_count(-1); self.my_blogs();";
			$this->view->error = 0;
			$this->view->message = "Blog was deleted.";
			$this->view->html = $this->view->render('success.tpl');
		}else{
			$this->view->error = 1;
			$this->view->message = "You can not delete blogs.";
			$this->view->html = $this->view->render('error.tpl');
		}
	}
	
	public function viewAction()
  {	
    $viewer = $this->_helper->api()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    $blog_id = $this->_getParam('blog_id', -1);
      if($blog_id > 0) {
          $subject = Engine_Api::_()->getItem('pageblog', $blog_id);
      }

    $this->view->comment_form_id = "blog-comment-form";
    
    if( $subject->getIdentity() )
    {
      $this->view->viewer = $viewer;
      
      if ($viewer->getIdentity() != $subject->user_id){
      	$subject->view_count++;
      	$subject->save();
      }
      
      $this->view->blog = $subject;
      $this->view->blogTags = $subject->tags()->getTagMaps();
    }
    $this->view->owner = $user = $subject->getOwner();
    
    $this->view->viewAllLikes = $this->_getParam('viewAllLikes', false);
    $this->view->likes = $likes = $subject->likes()->getLikePaginator();
		$this->view->page = $page = $this->_getParam('page');
		$this->view->comments = $this->getApi()->getComments($page);

    if( $viewer->getIdentity() && $this->view->isAllowedComment)
    {
      $this->view->form = $form = new Core_Form_Comment_Create();
      $form->addElement('Hidden', 'form_id', array('value' => 'blog-comment-form'));
      $form->populate(array(
        'identity' => $subject->getIdentity(),
        'type' => $subject->getType(),
      ));
    }

		$this->view->subject = Engine_Api::_()->core()->getSubject();
		$this->view->likeHtml = $this->view->render('comment/list.tpl');
		$this->view->likeUrl = $this->view->url(array('action' => 'like'), 'like_comment');
		$this->view->unlikeUrl = $this->view->url(array('action' => 'unlike'), 'like_comment');
		$this->view->hintUrl = $this->view->url(array('action' => 'hint'), 'like_comment');
		$this->view->showLikesUrl = $this->view->url(array('action' => 'list'), 'like_comment');
		$this->view->postCommentUrl = $this->view->url(array('action' => 'create'), 'like_comment');
		
      if($this->_getParam('format') == 'json')
          $this->view->html = $this->view->render('index/view.tpl');
      
  }
	
	public function editAction()
	{
		if ($this->view->isAllowedPost){
			$this->params['blog_id'] = $this->view->blog_id;
      $blog = $this->getTable()->getBlog($this->params);
			$this->view->blog = $this->fetchBlogData($blog);
      $this->view->photo = $blog->photo_id ? $blog->getPhoto() : false;
			$this->view->error = 0;
      $this->view->photo_html = $blog->photo_id ? $this->view->render('edit_photo.tpl') : false;
		}else{
			$this->view->error = 1;
			$this->view->eval = "self.my_blogs();";
			$this->view->message = "You can not edit blogs.";
			$this->view->html = $this->view->render('error.tpl');
		}
	}
	
	public function saveAction()
	{
		$this->view->eval = "self.my_blogs();";
		$values = $this->getValues();
		
		if (!$values['body'] || !$values['title'] || !$values['page_id'] || !$this->view->isAllowedPost){
			$this->view->message = "Can not save changes, check form fields and try again.";
			$this->view->html = $this->view->render('error.tpl');
		}
		
		$this->view->blog = $this->getTable()->postBlog($values);
		$this->view->eval = "self.view(".$values['blog_id'].");";
		$this->view->message = "Changes were saved successfully.";
		$this->view->html = $this->view->render('success.tpl');
	}
	
	public function createAction() 
	{
		$values = $this->getValues(); 
		$table = $this->getTable();
		
		if (!$values['body'] || !$values['title'] || !$values['page_id'] || !$this->view->isAllowedPost){		
			$this->view->message = "Blog was not created. Check form fields and try again or you do not have enough permissions.";
			$this->view->html = $this->view->render('error.tpl');
			return ;
		}
		
		$blog = $table->postBlog($values);

		$this->view->eval = "self.inc_count(); self.view(".$blog->getIdentity().");";
		$this->view->message = "Blog was created successfully.";
		$this->view->html = $this->view->render('success.tpl');
	}

  public function uploadPhotoAction()
  {
    if( !$this->_helper->requireUser()->checkRequire() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $values = $this->getRequest()->getPost();
    if( empty($values['Filename']) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
      return;
    }

    if( !isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name']) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

    $db = Engine_Api::_()->getDbtable('pageblogs', 'pageblog')->getAdapter();
    $db->beginTransaction();

    try
    {
      $photo = Engine_Api::_()->pageblog()->uploadPhoto($_FILES['Filedata']);

      $this->view->status = true;
      $this->view->name = $this->view->translate('Main Photo');
      $this->view->photo_id = $photo->getIdentity();
      $this->view->photo = $photo->toArray();

        $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
      return;
    }
  }

  public function removePhotoAction()
  {
    $photo_id = $this->_getParam('photo_id');
    $blog = null;

    if (!$photo_id){
      $blog_id = $this->getRequest()->getParam('blog_id');
      if ($blog_id){
        $blog = Engine_Api::_()->getItem('playlist', $blog_id);
        if ($blog){
          $photo_id = $blog->photo_id;
        }
      }
    }

    if ($photo_id == null){
      $this->view->success = false;
      $this->view->error   = $this->view->translate('pageblog_Not a valid request data.');
      return;
    }

    if( !$blog ) {
      $table = Engine_Api::_()->getDbTable('pageblogs', 'pageblog');
      $select = $table->select()->where('photo_id = ?', $photo_id);
      $blog = $table->fetchRow($select);
    }

    $db = $this->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      Engine_Api::_()->getApi('core', 'pageblog')->deletePhoto($photo_id);
      if ($blog){
        $blog->photo_id = 0;
        $blog->save();

        $search_api = Engine_Api::_()->getDbTable('search', 'page');
        $search_api->saveData($blog);
      }

      $this->view->status = true;

      $db->commit();
      $this->view->success = true;
    } catch (Exception $e) {
      $db->rollback();
      $this->view->success = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Unknown database error');
      throw $e;
    }
  }

	protected function fetchBlogData($blogObject)
	{
		$tags = $blogObject->tags()->getTagMaps();
    $tagString = '';
    foreach( $tags as $tagmap ){
      if( $tagString !== '' ) $tagString .= ', ';
      $tagString .= $tagmap->getTag()->getTitle();
    }
		return array(
			'blog_id' => $blogObject->getIdentity(),
			'title' => $blogObject->getTitle(),
			'body' => $blogObject->body,
			'tags' => $tagString,
      'photo_id' => $blogObject->photo_id
		);
	}
	
	protected function getValues()
	{
		return array(
			'body' => trim($this->_getParam('blog_body')),
			'title' => trim($this->_getParam('blog_title')),
			'page_id' => (int)$this->_getParam('page_id'),
			'tags' => trim(Engine_String::strip_tags($this->_getParam('blog_tags'))),
			'blog_id' => (int)$this->_getParam('blog_id'),
      'photo_id' => $this->_getParam('photo_id', 0),
		); 
	}
	
	protected function getApi()
  {
  	return Engine_Api::_()->getApi('core', 'pageblog'); 
  }

  protected function getPageApi()
  {
    return Engine_Api::_()->getApi('core', 'page');
  }

	protected function getTable() 
	{
		return Engine_Api::_()->getDbTable('pageblogs', 'pageblog');
	}
}