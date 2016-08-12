<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Category.php 2011-09-01 13:17:53 kirill $
 * @author     Kirill
 */

/**
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagedocument_Model_Category extends Core_Model_Item_Abstract
{
  protected $_parent_type = 'page';
  
  protected $_type = 'pagedocumentcategories';
  
  protected $_owner_type = 'user';
  
  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'page_view',      
      'page_id' => $this->getParentPage()->url,
      'content' => 'document',
      'content_id' => $this->getIdentity(),
    ), $params);

    $route = @$params['route'];
    unset($params['route']);

    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, true);
  }

  public function getPage()
  {
    return Engine_Api::_()->getItem('page', $this->page_id);
  }

  public function getParentPage()
  {
    return Engine_Api::_()->getItem('page', $this->page_id);
  }

  public function getTitle()
  {
    return $this->category_name;
  }

  public function getDocumentsCount($owner_id = null, $page_id = null)
  {
    $documents = Engine_Api::_()->getItemTable('pagedocument');
    $select = $documents->select();
    $select->where('category_id = ?', $this->category_id);

    if (!is_null($owner_id)) {
      $select->where('user_id = ?', $owner_id);
    }
    else {
    //  $select->where('status = ?', 'DONE');
    }

    if( $page_id ) {
      $select->where('page_id = ?', $page_id);
    }
    $result = $documents->getAdapter()->query($select);

    return ($result->rowCount());
  }
}