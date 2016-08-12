<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Pagedocuments.php 2011-09-01 13:17:53 kirill $
 * @author     Kirill
 */

/**
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagedocument_Model_DbTable_Pagedocuments extends Engine_Db_Table
{
  protected $_name = 'page_documents';
  protected $_rowClass = 'Pagedocument_Model_Pagedocument';




  public function getDocumentsList()
  {
    $res = Engine_Api::_()->loadClass("Pagedocument_Plugin_Google")->getListDocuments();
    return $res;
  }

    public function authApi(){
        $url_auth_api = Engine_Api::_()->loadClass("Pagedocument_Plugin_Google")->getClientUrl();
        return $url_auth_api;
    }


    public function getToken(){
        $token = Engine_Api::_()->loadClass("Pagedocument_Plugin_Google")->getClientToken();
        return $token;
    }


    public function authApiSave($key){
        $results = Engine_Api::_()->loadClass("Pagedocument_Plugin_Google")->codeApi($key);
        return $results;

    }

    public function uploadDoc($name,$file){
        $results = Engine_Api::_()->loadClass("Pagedocument_Plugin_Google")->uploadFile($name,$file);
        return $results;
    }

    public function downloadFile($id){
        $results = Engine_Api::_()->loadClass("Pagedocument_Plugin_Google")->downloadFiles($id);
        return $results;
    }







  public function getProcessedDocuments($params = array())
  {
    if (!empty($params['count']) && $params['count']) {
      return $this->getAdapter()->fetchOne($this->getSelect($params));
    }

    return $this->getPaginator($params);
  }

  public function getDocument($params = array())
  {
    $select = $this->getSelect($params);

    return $this->fetchRow($select);
  }

  public function getSelect($params = array())
  {
    $prefix = $this->getTablePrefix();
    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from($prefix . 'page_documents');

    if (!empty($params['count']) && $params['count']) {
      $select
        ->from($prefix . 'page_documents', array('count' => 'COUNT(*)'))
        ->group($prefix . 'page_documents.page_id');
    }

    $select
      ->joinLeft($prefix . 'users', $prefix . 'users.user_id = ' . $prefix . 'page_documents.user_id', array());

    if (!empty($params['page_id'])) {
      $select
        ->where($prefix . "page_documents.page_id = {$params['page_id']}");
    }

    if (!empty($params['user_id'])) {
      $select
        ->where($prefix."page_documents.user_id = {$params['user_id']}");
    }

    if (!empty($params['pagedocument_id'])) {
      $select
        ->where($prefix."page_documents.pagedocument_id = {$params['pagedocument_id']}");
    }

    if (!empty($params['status'])) {
      $select
        ->where($prefix . 'page_documents.status = ? ', $params['status']);
    }

    if (isset($params['category_id']) && $params['category_id'] != -1) {
      $select
        ->where($prefix.'page_documents.category_id = ? ', $params['category_id']);
    }

    $select->order($prefix."page_documents.pagedocument_id DESC");

    return $select;
  }

  public function getPaginator($params = array())
  {
    $select = $this->getSelect($params);
    $paginator = Zend_Paginator::factory($select);

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $ipp = !empty($params['ipp'])?$params['ipp']:10;
    $paginator->setItemCountPerPage($ipp);


    $p = !empty($params['p'])?$params['p']:1;
    $paginator->setCurrentPageNumber($p);

    return $paginator;
  }

  public function postDocument(array $values)
  {
    if (empty($values)) {
      return false;
    }

    $user = Engine_Api::_()->user()->getViewer();
    $title = $values['document_title'];
    $body = $values['document_body'];
    $page_id = $values['page_id'];
    $tags = preg_split('/[,]+/', $values['tags']);
    $values['creation_date'] = date('Y-m-d H:i:s');
    $values['modified_date'] = date('Y-m-d H:i:s');

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    $page = Engine_Api::_()->getItem('page', $page_id);

    try {
      $row = null;

      if (!empty($values['pagedocument_id']) && $values['pagedocument_id']) {
        $row = $this->getDocument(array('pagedocument_id' => $values['pagedocument_id']));
      }

      if (!$row) {
        $row = $this->createRow()->setFromArray($values);
        $row->user_id = $user->getIdentity();
      }

      $row->save();

      $search_api = Engine_Api::_()->getDbTable('search', 'page');
      $search_api->saveData($row);

      if ($tags) {
        $row->tags()->setTagMaps($user, $tags);
      }

      if (empty($values['pagedocument_id'])) {
        $this->addActivity($row, $page, $user, 'pagedocument_new');
      }

      $db->commit();
    }
    catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $row;
  }

  public function saveDocument(array $values)
  {
    if (empty($values)) {
      return false;
    }

    $user = Engine_Api::_()->user()->getViewer();
    $title = $values['document_title'];
    $body = $values['document_body'];
    $page_id = $values['page_id'];
    $tags = preg_split('/[,]+/', $values['tags']);
    $values['creation_date'] = date('Y-m-d H:i:s');
    $values['modified_date'] = date('Y-m-d H:i:s');

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    $page = Engine_Api::_()->getItem('page', $page_id);

    try {
      $row = null;

      if (!empty($values['pagedocument_id']) && $values['pagedocument_id']) {
        $row = $this->getDocument(array('pagedocument_id' => $values['pagedocument_id']));
      }

      if (!$row) {
        $row = $this->createRow()->setFromArray($values);
        $row->user_id = $user->getIdentity();
      }

      $row->save();

      $search_api = Engine_Api::_()->getDbTable('search', 'page');
      $search_api->saveData($row);

      if ($tags) {
        $row->tags()->setTagMaps($user, $tags);
      }

      if (empty($values['pagedocument_id'])) {
        $this->addActivity($row, $page, $user, 'pagedocument_new');
      }

      $db->commit();
    }
    catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $row;
  }

  public function addActivity(Core_Model_Item_Abstract $pagedocument, Core_Model_Item_Abstract $page, Core_Model_Item_Abstract $user, $type)
  {
    /*$api = Engine_Api::_()->getDbtable('actions', 'activity');
    $link = $pagedocument->getLink();

    $action = $api->addActivity($user, $page, $type, null, array('body' => strip_tags($pagedocument->body), 'link' => $link));
    $api->attachActivity($action, $pagedocument, Activity_Model_Action::ATTACH_DESCRIPTION);*/
  }

  public function getExistingDocs($page_id)
  {
    $select = $this->getSelect(array('page_id'=>$page_id));

    $row = $this->fetchAll($select);
    $cnt = count($row->toArray());

    if (!is_null($cnt)) {
      return $cnt;
    }

    return 0;
  }

  public function getUncategorizedDocumentsCount($owner_id = null)
  {

    $documents = $this;
    $select = $documents->select();
    $select->where('category_id = ?', 0);

    if(!is_null($owner_id)) {
       $select->where('user_id = ?', $owner_id);
    }
    else {
        $select->where('status = ?', 'DONE');
    }

    $result = $documents->getAdapter()->query($select);

    return ($result->rowCount());
  }
}