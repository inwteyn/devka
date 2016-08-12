<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ContentParentType.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Updates_View_Helper_ContentParentType extends Zend_View_Helper_Abstract
{
  public function contentParentType($content)
  {
    $contentTbl = Engine_Api::_()->getDbTable('content', 'updates');
    $select = $contentTbl->select()
      ->setIntegrityCheck(false)
      ->from(array('p' => $contentTbl->info('name')), array('name'))
      ->joinInner(array('c' => $contentTbl->info('name')), '`p`.id = `c`.parent_id', array())
      ->where('`c`.id = ?', $content['id']);

    return $contentTbl->getAdapter()->fetchOne($select);
  }
}