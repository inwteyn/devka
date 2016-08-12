<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: PageAlbum.php 2010-09-06 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagealbum_Model_Pagealbum extends Core_Model_Item_Collection
{
  protected $_parent_type = 'page';
  
  protected $_type = 'pagealbum';
  
  protected $_owner_type = 'user';
  
  protected $_searchColumns = array('title', 'description');
  
  protected $_collectible_type = "pagealbumphoto";

  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'page_view',
      'page_id' => $this->getParentPage()->url,
      'tab' => 'pagealbum',
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
  
  public function getPage()
  {
    return Engine_Api::_()->getItem('page', $this->page_id);
  }
  
  public function getParentPage()
  {
    return Engine_Api::_()->getItem('page', $this->page_id);
  }
  
  public function getAuthorizationItem()
  {
    return $this->getParent('page');
  }

  public function getPhotoUrl($type = null)
  {
  	$nophoto = Zend_Controller_Front::getInstance()->getBaseUrl()."/application/modules/Pagealbum/externals/images/nophoto_$type.png";
  	
    if( empty($this->photo_id) )
    {
      // This should probaby be done on delete
      $photo = $this->getFirstCollectible();
      if( $photo ) {
        $this->photo_id = $photo->getIdentity();
        $this->save();
        $file_id = $this->photo_id;
      }
      else {
        return $nophoto;
      }
    }
    else
    {
      $photo = Engine_Api::_()->getItem('pagealbumphoto', $this->photo_id);
      if( !$photo ){
        $this->photo_id = 0;
        $this->save();
        return $nophoto;
      } else {
        $file_id = $photo->file_id;
      }
    }

    if( !$file_id ) {
      return $nophoto;
    }

    $file = Engine_Api::_()->getApi('storage', 'storage')->get($file_id, $type);
    if( !$file ) {
      return $nophoto;
    }

    return $file->map();
  }
  
  protected function _insert()
  {
    if( !$this->page_id )
    {
      throw new Exception('Cannot create blog without page_id');
    }
    
    parent::_insert();
  }

	public function _delete()
	{
		$search_api = Engine_Api::_()->getDbTable('search', 'page');
		$search_api->deleteData($this);

		$this->removeTags();

		parent::_delete();
	}

	public function incrementViews()
  {
    $this->views++;
    $this->save();
  }

	public function removeTags()
	{
		$tagTable = Engine_Api::_()->getDbTable('tagMaps', 'page');
		$tagTable->delete(array('resource_id = ?' => $this->getIdentity(), 'resource_type = ?' => $this->getType()));
	}

  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   **/
  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   **/
  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

	public function tags()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'page'));
  }

  public function count()
  {
    $photoTable = Engine_Api::_()->getItemTable('pagealbumphoto');
    return $photoTable->select()
      ->from($photoTable, new Zend_Db_Expr('COUNT(pagealbumphoto_id)'))
      ->where('collection_id = ?', $this->pagealbum_id)
      ->limit(1)
      ->query()
      ->fetchColumn();
  }
}