<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagedocument
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagedocument
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagedocument_IndexController extends Core_Controller_Action_Standard
{
  protected $params;
  protected $subject;

  public function init()
  {
    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('pagedocument');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('like');
    $path = dirname($path) . '/views/scripts';

    $this->view->addScriptPath($path);

    $this->view->page_id = $this->_getParam('page_id');
    $this->view->viewer = $this->viewer = $this->_helper->api()->user()->getViewer();

    if (!is_null($this->view->page_id)) {

      $this->view->pageObject = $this->subject = Engine_Api::_()->getItem('page', $this->view->page_id);
      $this->view->isAllowedView = $this->getPageApi()->isAllowedView($this->subject);

      if (!$this->view->isAllowedView) {
        $this->view->isAllowedPost = false;
        $this->view->isAllowedComment = false;
        return;
      }

      $this->view->isAllowedPost = $this->getApi()->isAllowedPost($this->subject);
      $this->view->isAllowedComment = $this->getPageApi()->isAllowedComment($this->subject);
      $this->view->document_id = $document_id = $this->_getParam('document_id');

      if (!Engine_Api::_()->core()->hasSubject()) {
        if ($document_id !== null) {
          $subject = Engine_Api::_()->getItem('pagedocument', $document_id);
          if ($subject && $subject->getIdentity()) {
            Engine_Api::_()->core()->setSubject($subject);
          }
        }
      }

      $this->params = array('page_id' => $this->view->page_id, 'ipp' => $this->_getParam('ipp', 10), 'p' => $this->_getParam('p', 1), 'category_id' => $this->_getParam('category_id', -1));

      $this->scribd_api_key = Engine_Api::_()->getApi('settings', 'core')->pagedocument_api_key;
      $this->scribd_secret = Engine_Api::_()->getApi('settings', 'core')->pagedocument_secret_key;

      if (empty($this->scribd_api_key ) || empty($this->scribd_secret )) {
        $this->view->isAllowedPost = false;
      }

      $this->scribd = Engine_Api::_()->loadClass('Pagedocument_Plugin_Scribd');
      $this->scribd->setParams($this->scribd_api_key, $this->scribd_secret);

      $this->view->scribd = $this->scribd;

      $this->view->isCreationAllowed = true;

    }
  }

  public function indexAction()
  {
      $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('pagedocument');

      $p=1;
      if($this->_getParam('p')){
          $p =$this->_getParam('p');
      }

      $documents = $this->getTable()->getProcessedDocuments(array('page_id' => $this->_request->getParam('page_id'), 'ipp' => $this->_getParam('itemCountPerPage', 4), 'p' => $p));
      $this->view->documents = $documents;
      $this->view->ipp = $this->_getParam('itemCountPerPage', 4);

      if ($this->_getParam('titleCount', false) && $documents->getTotalItemCount() > 0){
          $this->_childCount = $documents->getTotalItemCount();
      }
      $this->view->html = $this->view->render('list.tpl');
  }

  public function mineAction()
  {
     $this->params['user_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
      $p=1;
      if($this->_getParam('p')){
          $p =$this->_getParam('p');
      }
     $documents = $this->getTable()->getProcessedDocuments(array('page_id' => $this->_request->getParam('page_id'), 'ipp' => $this->_getParam('itemCountPerPage', 4), 'p' => $p));
     $this->view->documents = $documents;
     $this->view->html = $this->view->render('list_edit.tpl');
  }

  public function deleteAction()
  {
    $this->view->eval = "self.my_documents();";

    if ($this->view->isAllowedPost) {
      $where = "page_id = {$this->view->page_id} AND pagedocument_id = {$this->view->document_id}";

      $table = $this->getTable();
      $document = $table->fetchRow($table->select()->where($where));
      $viewer = Engine_Api::_()->user()->getViewer();

      if ($document->user_id != $viewer->getIdentity()) {
        $this->view->error = 1;
        $this->view->message = $this->view->translate("pagedocument_User delete forbidden");
        $this->view->html = $this->view->render('error.tpl');
      }

      $search_api = Engine_Api::_()->getDbTable('search', 'page');
      $search_api->deleteData($document);

      if (!empty($document->doc_id)) {
        $this->scribd->my_user_id = $document->user_id;
        $this->scribd->delete($document->doc_id, $document->user_id);
      }

      $document->delete();

      $this->view->eval = "self.inc_count(-1); self.my_documents();";
      $this->view->error = 0;
      $this->view->message = $this->view->translate("pagedocument_Document deleted");
      $this->view->html = $this->view->render('success.tpl');
    }
    else {
      $this->view->error = 1;
      $this->view->message = $this->view->translate("pagedocument_User delete forbidden");
      $this->view->html = $this->view->render('error.tpl');
    }
  }

  public function viewAction()
  {
    $this->view->viewer = $viewer = $this->_helper->api()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $this->view->comment_form_id = "document-comment-form";


      $document_id = $this->_getParam('document_id', -1);
      if($document_id > 0){
          $subject = Engine_Api::_()->getItem('pagedocument', $document_id);
      }

    if ($subject->getIdentity()) {
      $this->view->viewer = $viewer;

      $subject->view_count++;
      $subject->save();

      $this->view->document = $subject;
      $this->view->documentTags = $subject->tags()->getTagMaps();


    }



    $this->view->owner = $user = $subject->getOwner();

    $this->view->canDelete = $subject->authorization()->isAllowed($viewer, 'edit');
    $this->view->canComment = Engine_Api::_()->page()->isAllowedComment($subject->getPage());

    $this->view->viewAllLikes = $this->_getParam('viewAllLikes', false);
    $this->view->likes = $likes = $subject->likes()->getLikePaginator();
    $this->view->page = $page = $this->_getParam('page');
    $this->view->comments = $this->getApi()->getComments($page);

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('like');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);

    if ($viewer->getIdentity() && $this->view->isAllowedComment) {
      $this->view->form = $form = new Core_Form_Comment_Create();
      $form->addElement('Hidden', 'form_id', array('value' => 'document-comment-form'));
      $form->populate(array(
        'identity' => $subject->getIdentity(),
        'type' => $subject->getType(),
      ));
    }

    if ($viewer->getIdentity() == 0) {
      $this->view->user_id = mt_rand(999, 999999);
    } else {
      $this->view->user_id = $viewer->getIdentity();
    }

    $this->view->session_id = session_id();
    $this->view->signature = md5($this->scribd_secret . 'document_id' . $subject->doc_id . 'session_id' . $this->view->session_id . 'user_identifier' . $this->view->user_id );

    $this->view->subject = Engine_Api::_()->core()->getSubject();

    $this->view->likeHtml = $this->view->render('comment/list.tpl');
    $this->view->likeUrl = $this->view->url(array('action' => 'like'), 'like_comment');
    $this->view->unlikeUrl = $this->view->url(array('action' => 'unlike'), 'like_comment');
    $this->view->hintUrl = $this->view->url(array('action' => 'hint'), 'like_comment');
    $this->view->showLikesUrl = $this->view->url(array('action' => 'list'), 'like_comment');
    $this->view->postCommentUrl = $this->view->url(array('action' => 'create'), 'like_comment');

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $this->view->width = $settings->getSetting('pagedocument.document.width','800');
    $this->view->height = $settings->getSetting('pagedocument.document.height','600');

    if($this->_getParam('format') == 'json')
        $this->view->html = $this->view->render('index/view.tpl');
  }

  public function geteditformAction()
  {
    $editForm = new Pagedocument_Form_Edit();
    $document_id = $this->_getParam('document_id');
    $document = Engine_Api::_()->getItem('pagedocument', $document_id);
    $params = $this->_getAllParams();

    if (!isset($params['values'])) {
      $editForm->populate($document->toArray());
    } else {
      $editForm->populate($params['values']);
    }

    $editForm->getElement('document_id')->setValue( $document->getIdentity() );
    $this->view->editForm = $editForm;
  }

  public function saveAction()
  {
    $this->view->eval = "self.my_documents();";

    $form = new Pagedocument_Form_Edit();
    $values = $this->_getAllParams();
    $document_id = $this->_getParam('document_id');

    if (!$form->isValid($values)) {
//      //@todo I don't know anything about this code =)).
//      $this->view->editForm = $form;
//      $html = $this->view->render('index/geteditform.tpl');
//      echo Zend_Json::encode(array('html'=>$html)); exit();

      $this->view->error = 1;
      $this->view->eval = "self.edit($document_id);";
      $this->view->values = $values;
      $this->view->message = $this->view->translate("pagedocument_Form filling error");
      $this->view->html = $this->view->render('error.tpl');

      return;
    }

    $document = Engine_Api::_()->getItem('pagedocument', $document_id);
    $document->document_title = $this->_getParam('document_title');
    $document->document_description = $this->_getParam('document_description');
    $document->document_tags = $this->_getParam('document_tags');
    $document->category_id = $this->_getParam('category_id');
    $document->download_allow = $this->_getParam('download_allow');
    $document->save();

    $this->view->eval = "self.view(" . $values['document_id'] . ");";
    $this->view->message = $this->view->translate("pagedocument_Changes saved");
    $this->view->html = $this->view->render('success.tpl');
  }







  public function getcreateformAction()
  {
      $table = Engine_Api::_()->getDbTable('pagedocuments', 'pagedocument');
      $viewer = Engine_Api::_()->user()->getViewer();

     if($viewer->isAdmin()){
      $token  = $table->getToken();
      $apiForm = new Pagedocument_Form_Api();
     }
      if($viewer->isAdmin()&&$token){
          if(!$this->_getParam('pagedocument_auth_api')){
              $url_api = $table->authApi();
              if(isset($url_api)){
                  $apiForm->addNotice("<a href='".$url_api."' target='_blank'>Open the following link in your browser for registr api</a>");
                  $this->view->createFormApi = $apiForm;
              }
          }else{
           $table->authApiSave($this->_getParam('pagedocument_auth_api'));
          }
      }else{

        $this->view->createForm = $createForm = new Pagedocument_Form_Create();

          $params = $this->_getAllParams();
        if (isset($params['values'])) {
          $createForm->populate($params['values']);
        }
      }
  }


    public function downloadAction(){
        $table_google = Engine_Api::_()->getDbTable('pagedocuments', 'pagedocument');
        $res = $table_google->downloadFile($this->_getParam('id'));
        return $res;
    }


    public function createAction()
  {

    $table_google = Engine_Api::_()->getDbTable('pagedocuments', 'pagedocument');

    $viewer = Engine_Api::_()->user()->getViewer();

      if($viewer->isAdmin()){
    $upload_on_google = $table_google->uploadDoc($this->_getParam('file_id'),'.'.$this->_getParam('file_path'));
      }

    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }



    if (empty($this->scribd_api_key) || empty($this->scribd_secret)) {
      $this->view->error = 1;
      $this->view->eval = "self.list();";
      $this->view->message = $this->view->translate("pagedocument_Scribd_credentials_need");
      $this->view->html = $this->view->render('error.tpl');
    }

    $form = new Pagedocument_Form_Create();
    $values = $this->_getAllParams();

    if (!$form->isValid($values) || empty($values['file_id']) || empty($values['file_path'])) {
      $this->view->error = 1;
      $this->view->eval = "self.create();";
      $this->view->values = $values;
      $this->view->message = $this->view->translate("pagedocument_Form filling error");
      $this->view->html = $this->view->render('error.tpl');

      return;
    }

    unset($values['action']);
    unset($values['module']);
    unset($values['controller']);
    unset($values['rewrite']);
    unset($values['no_cache']);
    unset($values['format']);

    $table = $this->getTable();

    $download_allow = $values['download_allow'];
    $secure_allow = $values['secure_allow'];

    if( !$secure_allow ) {
      $download_allow = 'download-pdf';
    }

    $documentRow = $table->createRow();
    $documentRow->setFromArray($values);
    $documentRow->document_tags = $values['document_tags'];
    $documentRow->user_id = $viewer->getIdentity();
    $documentRow->save();

    $documentRow->download_allow = $download_allow;
    $documentRow->secure_allow = $secure_allow;

    if (!empty($values['file_id']) && !empty($values['file_path'])) {
      $this->scribd->my_user_id = $viewer->getIdentity();
      $data = $this->uploadToSCRIBD($values['file_path'], $download_allow, $secure_allow);

      $documentRow->doc_id = $data['doc_id'];
     // $documentRow->access_key = $data['access_key'];

      $documentRow->filesize = $values['file_size'];
      $documentRow->storage_path = $values['file_path'];
      $documentRow->filename_id = $values['file_id'];
    }

     if($upload_on_google){
         $documentRow->file_link_google = $upload_on_google;
     }
    $documentRow->save();

    $tags = preg_split('/[,]+/', $values['document_tags']);
    if ($tags) {
      $documentRow->tags()->setTagMaps($viewer, $tags);
    }

    $search_api = Engine_Api::_()->getDbTable('search', 'page');

    $params = array();
    $params['body'] = $documentRow->document_description;
    $params['title'] = $documentRow->document_title;
    $params['page_id'] = $documentRow->page_id;
    $params['object'] = $documentRow->getType();
    $params['object_id'] = $documentRow->getIdentity();
    $params['photo_id'] = 0;

    $search_api->saveDataFromArray($params);

    $page = Engine_Api::_()->getItem('page', $values['page_id']);
    $api = Engine_Api::_()->getDbtable('actions', 'activity');
    $action = $api->addActivity($viewer, $page, 'pagedocument_new', null, array('tag'=>$values['document_tags'],'title_tag' =>$documentRow->document_title ));

    if( $action ) {
      Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $documentRow);
    }

    $this->view->eval = "self.inc_count(); self.my_documents();";
    $this->view->message = $this->view->translate("pagedocument_Document created");
    $this->view->html = $this->view->render('success.tpl');
  }

  public function uploadToSCRIBD($path_to_file, $download_allow, $secure_allow)
  {
    try {
      $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
      $base_path = Zend_Controller_Front::getInstance()->getBaseUrl();
      $doc_path = $host_url . $base_path . "/";
      $doc_path = str_replace("index.php/", '', $doc_path);
      $upload_to_scribd_url = $doc_path . $path_to_file;

      $accesss = Engine_Api::_()->getApi('settings', 'core')->getSetting('pagedocument.default.visibility', 'public');
      $result = $this->scribd->uploadFromUrl("$upload_to_scribd_url", NULL, $accesss, $download_allow, $secure_allow);
      $result['local_path'] = $upload_to_scribd_url;
    }
    catch (Exception $e) {
      $message = $e->getMessage();
      $this->view->excep_error = 1;
      $this->view->excep_message = $message;
    }

    return $result;
  }

  protected function fetchDocumentData($documentObject)
  {
    $tags = $documentObject->tags()->getTagMaps();
    $tagString = '';

    foreach ($tags as $tagmap) {
      if ($tagString !== '') {
        $tagString .= ', ';
      }

      $tagString .= $tagmap->getTag()->getTitle();
    }

    return array(
      'document_id' => $documentObject->getIdentity(),
      'title' => $documentObject->getTitle(),
      'body' => $documentObject->body,
      'tags' => $tagString
    );
  }

  protected function getApi()
  {
    return Engine_Api::_()->getApi('core', 'pagedocument');
  }

  protected function getPageApi()
  {
    return Engine_Api::_()->getApi('core', 'page');
  }

  protected function getTable()
  {
    return Engine_Api::_()->getDbTable('pagedocuments', 'pagedocument');
  }

  public function uploadDocumentAction()
  {
    $error_msg = $this->view->translate('pagedocument_Error');

    if (!$this->viewer) {
      $this->view->status = false;
      $this->view->error = $error_msg;
      return;
    }

    if (!$this->getRequest()->isPost() || !$this->getRequest()->getParam('Filename')) {
      $this->view->status = false;
      $this->view->error = $error_msg;
      return;
    }

    if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
      $this->view->status = false;
      $this->view->error = $error_msg;
    }

    $db = $this->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $document_file = Engine_Api::_()->getApi('core', 'pagedocument')->uploadDocument($_FILES['Filedata']);

      $this->view->file_size = $document_file['file_size'];
      $this->view->file_path = $document_file['file_url'];
      $this->view->file_id = $document_file['file_id'];
      $this->view->status = true;
      $db->commit();
    }
    catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = $error_msg;
      throw $e;
    }
  }

  public function removeDocumentAction()
  {
    $error_msg = $this->view->translate('pagedocument_Error');
    $doc_id = $this->_getParam('doc_id');

    if (empty($this->scribd_api_key) || empty($this->scribd_secret)) {
      $this->view->error = 1;
      $this->view->eval = "self.list();";
      $this->view->message = "pagedocument_Scribd_credentials_need";
      $this->view->html = $this->view->render('error.tpl');
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $doc = Engine_Api::_()->getItem('pagedocument', $doc_id);

    if ($doc) {
      if ($doc->user_id != $viewer->getIdentity()) {
        $this->view->error = 1;
        $this->view->eval = "self.list();";
        $this->view->message = "pagedocument_No permissions";
        $this->view->html = $this->view->render('error.tpl');
      }
    }

    if (!$this->viewer || !$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = $error_msg;
    }


    if (!$doc_id) {
      return;
    }

    $db = $this->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      Engine_Api::_()->getApi('core', 'pagedocument')->deleteDocumentFile($doc_id);
      $this->view->status = true;
      $db->commit();
    }
    catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = $error_msg;
      throw $e;
    }
  }


}