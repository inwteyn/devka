<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Modules.php 2010-07-02 19:52 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecore_Model_DbTable_Modules extends Engine_Db_Table
{
  protected $_rowClass = "Hecore_Model_Module";
  protected $_primary = "name";
  private $_productCustomKeys = array(
    'headvancedalbum' => 'advancedalbum',
    'hebadge' => 'badges',
    'appmanager' => 'iphoneapp',
    'like' => 'likes',
    'page' => 'pages',
    'pagealbum' => 'page_albums',
    'pageblog' => 'page_blogs',
    'pagecontact' => 'page_contact',
    'pagediscussion' => 'page_discussions',
    'pagedocument' => 'page_document',
    'pageevent' => 'page_events',
    'pagefaq' => 'page_faq',
    'pagemusic' => 'page_music',
    'pagevideo' => 'page_videos',
    'hequestion' => 'questions',
  );

  public function getProductKeyByModuleName($moduleName)
  {
    return isset($this->_productCustomKeys[$moduleName]) ? $this->_productCustomKeys[$moduleName] : $moduleName;
  }

  public function findByName($moduleName)
  {
    if (!$moduleName) {
      return false;
    }
    $productName = $this->getProductKeyByModuleName($moduleName);

    $select = $this->select();
    $select->where("name = ?", $productName);

    return $this->fetchRow($select);
  }

  public function isModuleEnabled($name)
  {
    $isModuleEnabled = false;

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($name)) {
      if ($this->findByName($name)) $isModuleEnabled = true;
    }

    return $isModuleEnabled;
  }

  public function getAllModules()
  {
    $select = $this->select();
    return $this->fetchAll($select);
  }
}