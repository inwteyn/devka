<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: PageBlog.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pageblog_Model_Pageblog extends Core_Model_Item_Abstract
{
	protected $_parent_type = 'page';
	
	protected $_type = 'pageblog';
	
  protected $_owner_type = 'user';
  
	public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'page_view',      
      'page_id' => $this->getParentPage()->url,
    	'tab' => 'blog',
    	'content_id' => $this->getIdentity(),
    ), $params);
    
    $route = @$params['route'];
    unset($params['route']);
    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, true);
  }
  
  public function getLink()
	{
		return sprintf("<a href='%s'>%s</a>", $this->getHref(), $this->getTitle());
	}
  
  public function tags()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'page'));
  }
  
  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }
  
  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }
  
	public function getAuthorizationItem()
  {
    return $this->getParent('page');
  }
  
	public function getPage()
  {
    return Engine_Api::_()->getItem('page', $this->page_id);
  }
  
	public function getParentPage()
  {
    return Engine_Api::_()->getItem('page', $this->page_id);
  }
  
  public function delete()
  {
    $table = $this->getTable();
  	$db = $table->getAdapter();

    $prefix = $table->getTablePrefix();

  	$where = "resource_type = '{$this->getType()}' AND resource_id = {$this->getIdentity()}";
  	
  	$db->delete($prefix.'core_comments', $where);
  	$db->delete($prefix.'core_likes', $where);
  	$db->delete($prefix.'core_tagmaps', $where);
  	
  	$where = "object_type = '{$this->getType()}' AND object_id = {$this->getIdentity()}";
  	$db->delete($prefix.'activity_notifications', $where);

		$search_api = Engine_Api::_()->getDbTable('search', 'page');
		$search_api->deleteData($this);

		$this->removeTags();

    // Remove Photo
    if ($this->photo_id){
      $photo = Engine_Api::_()->storage()->get($this->photo_id);
      if ($photo){
        $photo->delete();
      }
    }

  	parent::delete();
  }

	public function removeTags()
	{
		$tagTable = Engine_Api::_()->getDbTable('tagMaps', 'page');
		$tagTable->delete(array('resource_id = ?' => $this->getIdentity(), 'resource_type = ?' => $this->getType()));
	}
  
	protected function _insert()
  {
    if( !$this->page_id )
    {
      throw new Exception('Cannot create blog without page_id');
    }
    
    parent::_insert();
  }

  public function getPhoto()
  {
    if (!$this->photo_id){
      return false;
    }

    $storage = Engine_Api::_()->storage();
    return $storage->get($this->photo_id);
  }

  public function getMediaType()
  {
    return $this->getType();
  }
}