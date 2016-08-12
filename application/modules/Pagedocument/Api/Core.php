<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2011-09-01 13:17:53 kirill $
 * @author     Kirill
 */

/**
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Pagedocument_Api_Core extends Page_Api_Core
{
  public function getTable()
  {
    return Engine_Api::_()->getDbTable('pagedocuments', 'pagedocument');
  }

  public function getDocuments()
  {
    $table = $this->getTable()->fetchAll();

    return $table;
  }

  public function getCategories()
  {
    $cats = Engine_Api::_()->getDbtable('categories', 'pagedocument')->getFetched();

    return $cats;
  }

  public function getCategory($id)
  {
    $cats = Engine_Api::_()->getDbtable('categories', 'pagedocument')->find($id)->current();

    return $cats;
  }

  public function uploadDocument($document, $params = array())
  {
    if ($document instanceof Zend_Form_Element_File) {
      $file = $document->getFileName();
    } else if (is_array($document) && !empty($document['tmp_name'])) {
      $file = $document['tmp_name'];
    } else if (is_string($document) && file_exists($document)) {
      $file = $document;
    } else {
      throw new Exception('Invalid argument passed to uploadDocument: ' . print_r($document,1));
    }

    try
    {
      $extension = ltrim(strrchr($document['name'], '.'), '.');
      $name = basename($file);
      $path = dirname($file);
      $mainName = $path . '/' . $document['name'] . '.' . $extension;
      rename($path.'/'.$name, $mainName );
      $params = array_merge(array(
        'name' => $mainName,
        'parent_type' => 'pagedocument',
//      'extension' => $extension,
      ), $params);
      $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
      $filesTable->createFile($mainName, $params);
      $document_return = Engine_Api::_()->storage()->create($mainName, $params);
      $document_return->save();
      
      if (!empty($document_return->file_id)) {
        $document_info = array(
          'file_size' => $document_return->size,
          'file_path' => $document_return->storage_path,
          'file_url' => $document_return->map(),
          'file_id' => $document_return->getIdentity()
        );

        return $document_info;
      }
    }
    catch (Exception $e)
    {
       $msg =  $e->getMessage();

       return $msg;
    }
  }

  public function deletePhoto($doc_id)
  {
    $storage = Engine_Api::_()->storage();

    $doc = $storage->get($doc_id);

    if ($doc) {
      $doc->delete();
    }
  }

  public function getComments($page = null)
  {
    $subject = Engine_Api::_()->core()->getSubject();

    if (null !== $page) {

      $commentSelect = $subject->comments()->getCommentSelect();
      $commentSelect->order('comment_id ASC');
      $comments = Zend_Paginator::factory($commentSelect);
      $comments->setCurrentPageNumber($page);
      $comments->setItemCountPerPage(10);

    } else {

      $commentSelect = $subject->comments()->getCommentSelect();
      $commentSelect->order('comment_id DESC');
      $comments = Zend_Paginator::factory($commentSelect);
      $comments->setCurrentPageNumber(1);
      $comments->setItemCountPerPage(4);

    }

    return $comments;
  }

  public function getInitJs($content_info, $subject)
  {
    if (empty($content_info))
        return false;

    $content = $content_info['content'];
    $content_id = $content_info['content_id'];
    $res = "page_document.init_document();";

    if( $subject->isTimeline() ) {
      $tbl = Engine_Api::_()->getDbTable('content', 'page');
      $id = $tbl->select()->from($tbl->info('name'), array('content_id'))
        ->where('page_id = ?', $subject->getIdentity())
        ->where("name = 'pagedocument.profile-document'")
        ->where('is_timeline = 1')
        ->query()
        ->fetch();
      $res = "tl_manager.fireTab('{$id['content_id']}');";
    }
    if ($content == 'document'){
        $pagedocument = Engine_Api::_()->getItem('pagedocument', $content_id);
        if (!$pagedocument)
            return false;
        return $res;
    }elseif ($content == 'pagedocuments'){/// for SEO by Kirill
        if($content_id == 1)
            return $res;
        else
            return $res;
    }
    return false;
  }

  public function isAllowedPost( $page ) {
    if( !$page )
      return false;
    $auth = Engine_Api::_()->authorization()->context;
    return $auth->isAllowed($page, Engine_Api::_()->user()->getViewer(), 'doc_posting');
  }

}