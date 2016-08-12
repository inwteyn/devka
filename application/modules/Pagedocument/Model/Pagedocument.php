<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Pagedocument.php 2011-09-01 13:17:53 kirill $
 * @author     Kirill
 */

/**
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagedocument_Model_Pagedocument extends Core_Model_Item_Abstract
{
  protected $_parent_type = 'page';
  protected $_type = 'pagedocument';
  protected $_owner_type = 'user';
  protected $_searchTriggers = null;

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

  public function getAuthorizationItem()
  {
    return $this->getParent('page');
  }

  public function getParentPage()
  {
    return Engine_Api::_()->getItem('page', $this->page_id);
  }

  public function tags()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'page'));
  }

  public function getLink()
  {
    return sprintf("<a href='%s'>%s</a>", $this->getHref(), $this->getTitle());
  }

  public function getTitle()
  {
    return $this->document_title;
  }

  public function getCategory()
  {

    $table = Engine_Api::_()->getDbTable('categories', 'pagedocument');
    $params['category_id'] = $this->category_id;

    $res = $table->getCategory($params);

    if ($res) {
      return $res;
    }

    return false;
  }

  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  public function checkState()
  {
    $scribd = Engine_Api::_()->loadClass('Pagedocument_Plugin_Scribd');
    $scribd_api_key = Engine_Api::_()->getApi('settings', 'core')->pagedocument_api_key;
    $scribd_secret = Engine_Api::_()->getApi('settings', 'core')->pagedocument_secret_key;
    $scribd->setParams($scribd_api_key, $scribd_secret);
    $this->status = $scribd->getConversionStatus($this->doc_id, $this->user_id);
    $this->save();
  }
}