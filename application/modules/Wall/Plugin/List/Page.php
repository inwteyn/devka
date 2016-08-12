<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Page.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Plugin_List_Page extends Wall_Plugin_List_Abstract
{
  public function getSelect(User_Model_User $user, array $params = array())
  {
    $db = Engine_Db_Table::getDefaultAdapter();

    $table = Engine_Api::_()->getDbTable('pages', 'page');
    $like = Engine_Api::_()->getDbTable('likes', 'core');
    $membership = Engine_Api::_()->getDbTable('membership', 'page');

    $user_id = $user->getIdentity();

    $select = $db->select()
        ->from(array('p' => $table->info('name')), new Zend_Db_Expr("'page' AS `type`, p.page_id AS id"))
        ->joinLeft(array('l' => $like->info('name')), "l.resource_type = 'user' AND l.resource_id = p.page_id AND l.poster_type = 'page' AND l.poster_id = $user_id", array())
        ->joinLeft(array('m' => $membership->info('name')), "m.resource_id = p.page_id AND m.user_id = $user_id AND m.active = 1", array())
        ->where(new Zend_Db_Expr('NOT ISNULL(l.resource_id) OR NOT ISNULL(m.resource_id)'));

    if (!empty($params['search'])){
      $select->where('p.title LIKE ? OR p.description LIKE ?', '%'. $params['search'] .'%');
    }

    return $select;

  }


}