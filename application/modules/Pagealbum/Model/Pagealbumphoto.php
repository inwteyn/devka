<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: PageAlbumPhoto.php 2010-09-06 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagealbum_Model_Pagealbumphoto extends Core_Model_Item_Collectible
{
  protected $_searchColumns = array('title', 'description');

  protected $_type = 'pagealbumphoto';

  protected $_owner_type = 'user';

  protected $_parent_type = 'pagealbum';

  protected $_collection_type = "pagealbum";

  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'page_view',
      'reset' => true,
      'content' => 'pagealbumphoto',
      'page_id' => $this->getParent()->getParentPage()->url,
      'album_id' => $this->collection_id,
      'content_id' => $this->getIdentity(),
    ), $params);

    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  public function getCollection()
  {
    return Engine_Api::_()->getItem('pagealbum', $this->collection_id);
  }

  public function getLink()
  {
    return sprintf("<a class='pagealbum_photo_box' href='%s'><img src='%s' border='0' /><br /><span>%s</span></a>", $this->getHref(), $this->getPhotoUrl('thumb.normal'), $this->getTitle());
  }

  public function getPage()
  {
    return $this->getCollection()->getPage();
  }

  public function getOwner($recurseType = null)
  {
    return Engine_Api::_()->getItem('user', $this->owner_id);
  }

  public function getParent($type = null)
  {
    if( null === $type ) {
      return $this->getCollection();
    } else {
      return $this->getCollection()->getParent($type);
    }
  }

  /**
   * Gets a url to the current photo representing this item. Return null if none
   * set
   *
   * @param string The photo type (null -> main, thumb, icon, etc);
   * @return string The photo url
   */
  public function getPhotoUrl($type = null)
  {
    $photo_id = $this->file_id;
    if( !$photo_id ) {
      return null;
    }

    $file = Engine_Api::_()->getApi('storage', 'storage')->get($photo_id, $type);
    if( !$file ) {
      return null;
    }

    return $file->map();
  }


  public function getAuthorizationItem()
  {
    return $this->getCollection()->getAuthorizationItem();
  }

  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   * */
  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   * */
  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  /**
   * Gets a proxy object for the tags handler
   *
   * @return Engine_ProxyObject
   * */
  public function tags()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'page'));
  }

  public function isOwner(Core_Model_Item_Abstract $user)
  {
    if( empty($this->collection_id) ) {
      return (($this->owner_id == $user->getIdentity()));
    }

    return parent::isOwner($user);
  }

  protected function _postDelete()
  {
    // This is dangerous, what if something throws an exception in postDelete
    // after the files are deleted?
    try {

      $search_api = Engine_Api::_()->getDbTable('search', 'page');
      $search_api->deleteData($this);

      $file = Engine_Api::_()->getApi('storage', 'storage')->get($this->file_id, null);
      if ($file) $file->remove();
      $file = Engine_Api::_()->getApi('storage', 'storage')->get($this->file_id, 'thumb.normal');
      if ($file) $file->remove();
      $file = Engine_Api::_()->getApi('storage', 'storage')->get($this->file_id, 'thumb.mini');
      if ($file) $file->remove();
      $file = Engine_Api::_()->getApi('storage', 'storage')->get($this->file_id, 'thumb.icon');
      if ($file) $file->remove();

      $album = $this->getCollection();
      if ($album){
        $nextPhoto = $this->getNextCollectible();

        if( ($album instanceof Core_Model_Item_Collection) && ($nextPhoto instanceof Core_Model_Item_Collectible) &&
          (int) $album->photo_id == (int) $this->getIdentity() ) {
          $album->photo_id = $nextPhoto->getIdentity();
          $album->save();
        }
      }
    } catch( Exception $e ) {
      // @todo should we completely silence the errors?
      //throw $e;
    }

    parent::_postDelete();
  }

  public function setPhoto($photo)
  {
    if ($photo instanceof Zend_Form_Element_File) {
      $file = $photo->getFileName();
      $fileName = $file;
    } else if ($photo instanceof Storage_Model_File) {
      $file = $photo->temporary();
      $fileName = $photo->name;
    } else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
      $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
      $file = $tmpRow->temporary();
      $fileName = $tmpRow->name;
    } else if (is_array($photo) && !empty($photo['tmp_name'])) {
      $file = $photo['tmp_name'];
      $fileName = $photo['name'];
    } else if (is_string($photo) && file_exists($photo)) {
      $file = $photo;
      $fileName = $photo;
    } else {
      throw new User_Model_Exception('invalid argument passed to setPhoto');
    }

    if (!$fileName) {
      $fileName = $file;
    }

    $name = basename($file);
    $extension = ltrim(strrchr($fileName, '.'), '.');
    $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => $this->getType(),
      'parent_id' => $this->getIdentity(),
      'user_id' => $this->owner_id,
      'name' => $fileName,
    );

    // Save
    $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

    // Resize image (main)
    $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(720, 720)
      ->write($mainPath)
      ->destroy();

    // Resize image (normal)
    $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(210, 240)
      ->write($normalPath)
      ->destroy();

    // Store
    try {
      $iMain = $filesTable->createFile($mainPath, $params);
      $iIconNormal = $filesTable->createFile($normalPath, $params);

      $iMain->bridge($iIconNormal, 'thumb.normal');
    } catch (Exception $e) {
      // Remove temp files
      @unlink($mainPath);
      @unlink($normalPath);
      @unlink($file);
      // Throw
      if ($e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE) {
        throw new Album_Model_Exception($e->getMessage(), $e->getCode());
      } else {
        throw $e;
      }
    }

    // Remove temp files
    @unlink($mainPath);
    @unlink($normalPath);
    @unlink($file);

    // Update row
    $this->modified_date = date('Y-m-d H:i:s');
    $this->file_id = $iMain->file_id;
    $this->save();

    // Delete the old file?
    if (!empty($tmpRow)) {
      $tmpRow->delete();
    }

    return $this;
  }

}