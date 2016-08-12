<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Review.php 2010-07-02 19:53 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Rate_Model_Productreview extends Core_Model_Item_Abstract
{
  protected $_owner_type = 'user';
  protected $_type = 'productreview';
  protected $_parent_type = 'product';
  protected $_searchColumns = array('title', 'body');

  public function getHref($params = array())
  {
    $product = $this->getProduct();
    $params = array_merge(array(
      'route' => 'store_profile',
      'product_id' => $this->product_id,
      'title' => $this->getSlug($product->getTitle()),
      'content' => 'productreview',
      'content_id' => $this->getIdentity()
    ), $params);

    $route = @$params['route'];
    unset($params['route']);
    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, true);
  }

  public function getLink()
	{
		return sprintf("<a href='%s'>%s</a>", $this->getHref(), $this->getTitle());
	}

  public function getProduct()
  {
    return Engine_Api::_()->getItem('store_product', $this->product_id);
  }

  public function delete(){

    // Delete Votes
    $tbl = Engine_Api::_()->getDbTable('votes', 'rate');
    $tbl->delete(array(
      'review_id = ?' => $this->getIdentity()
    ));

    /*$tbl_search = Engine_Api::_()->getDbTable('search', 'page');
    $tbl_search->delete(array(
      'object = ?' => 'pagereview',
      'object_id = ?' => $this->getIdentity()
    ));*/

    // Delete Actions
    $tbl = Engine_Api::_()->getDbTable('attachments', 'activity');
    $action_ids = $tbl->select()
        ->from($tbl->info('name'), 'action_id')
        ->where('type = ?', $this->getType())
        ->where('id = ?', $this->getIdentity())
        ->query()
        ->fetchAll(Zend_Db::FETCH_COLUMN);

    $tbl->delete(array(
      'type = ?' => $this->getType(),
      'id = ?' => $this->getIdentity()
    ));

    if ($action_ids){
      Engine_Api::_()->getDbTable('actions', 'activity')->delete(array(
        'action_id IN (?)' => $action_ids
      ));
    }

    return parent::delete();
  }

  /*public function getAuthorizationItem()
  {
    return $this->getPage('page');
  }*/

  public function getDescription()
  {
    $tmpBody = Engine_String::strip_tags($this->body);
    return ( Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody );
  }

  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }


  public function getTypes() {
    $types = Engine_Api::_()->getApi('core', 'rate')->getProductTypes($this->product_id);
    foreach ($types as $key => $type) {
      if (isset($vote_list[$type->type_id])) {
        $types[$key]->value = $vote_list[$type->type_id];
      }
    }
    return $types;
  }
  public function getRichContent($view = false, $params = array())
  {
    try {
      $view = new Zend_View();
      $scriptPath = APPLICATION_PATH
        . DIRECTORY_SEPARATOR
        . "application"
        . DIRECTORY_SEPARATOR
        . "modules"
        . DIRECTORY_SEPARATOR
        . 'Rate'
        . DIRECTORY_SEPARATOR
        . 'views'
        . DIRECTORY_SEPARATOR
        . 'scripts';
      $EngineHelperPath = 'Engine/View/Helper/';

      $view->setScriptPath($scriptPath);
      $view->addHelperPath($EngineHelperPath, implode('_', explode('/', $EngineHelperPath)));
      $view->addHelperPath(APPLICATION_PATH . '/application/modules/Rate/View/Helper', 'Rate_View_Helper');
      $view->addHelperPath(APPLICATION_PATH . '/application/modules/Hecore/View/Helper', 'Hecore_View_Helper');

        $tbl_vote = Engine_Api::_()->getDbTable('votes', 'rate');
        $select = $tbl_vote->select()
          ->where('review_id = ?', $this->getIdentity());
        $votes = $tbl_vote->fetchAll($select);

        $vote_list = array();
        foreach ($votes as $vote) {
          $vote_list[$vote->type_id] = $vote->rating;
        }

      $view->assign('review', $this);
      $view->assign('product', $this->getProduct());
      $view->assign('rating', $vote->rating);

      $richContent = $view->render('_productreview_richcontent.tpl');
    } catch (Exception $e) {
      print_die("WTF ERROR " . $e->getMessage(), 0, '192.168.0.20');
    }
    return $richContent;
  }

}