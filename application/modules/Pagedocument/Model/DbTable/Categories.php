<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Categories.php 2011-09-01 13:17:53 kirill $
 * @author     Kirill
 */

/**
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagedocument_Model_DbTable_Categories extends Engine_Db_Table
{
  protected $_rowClass = 'Pagedocument_Model_Category';
  protected $_name = 'page_documents_categories';
  protected $_primary = 'category_id';

  public function getCategory($params = array())
  {
    $select = $this->getSelect($params);
    $obj = $this->fetchRow($select);
    if($obj)
        return $obj->category_name;
      return false;
  }

  public function getSelect($params = array())
  {
    $select = $this->select()
      ->setIntegrityCheck(false)
      ;

    $select->from(array('category' => 'engine4_page_documents_categories'));
    $select->joinLeft(array('doc' => 'engine4_page_documents'), 'doc.category_id = category.category_id', array());

    if( !empty($params['page_id']) ){
      $select->where('doc.page_id = ?', $params['page_id']);
     // $select->where('doc.status = ?', 'DONE');
    }

    if (isset($params['category_id'])) {
      $select->where('category.category_id=?', $params['category_id']);
    }

    $select->order("category.order ASC");
    $select->group('category.category_id');

    return $select;
  }

  public function getPaginator($params = array())
  {
    $select = $this->getSelect($params);
    $paginator = Zend_Paginator::factory($select);

    if (!empty($params['ipp'])) {
      $paginator->setItemCountPerPage($params['ipp']);
    }

    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }

    return $paginator;
  }

  public function getFetched($params = array())
  {
    $select = $this->getSelect($params);
    $t1 = $this->getAdapter()->query($select);
    $t2 = $t1->fetchAll();

    return $t2;
  }
}