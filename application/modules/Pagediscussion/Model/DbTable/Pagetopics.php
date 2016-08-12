<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Pagetopics.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagediscussion_Model_DbTable_Pagetopics extends Engine_Db_Table
{
  protected $_name = 'page_topics';
  protected $_primary = 'topic_id';
  protected $_rowClass = 'Pagediscussion_Model_Pagetopic';

  public function getPaginator($page_id, $page = 1, $ipp = 10)
  {
    // Settings
    $select = $this->select()
        ->where('page_id = ?', $page_id)
        ->order('sticky DESC')
        ->order('modified_date DESC');

    $paginator = Zend_Paginator::factory($select);
    $paginator->setDefaultItemCountPerPage($ipp);
    $paginator->setCurrentPageNumber($page);

    return $paginator;

  }

}