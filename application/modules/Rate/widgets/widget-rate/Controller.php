<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-07-02 19:53 vadim $
 * @author     Vadim
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Rate_Widget_WidgetRateController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
	  if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('rate')) {
      return $this->setNoRender();
	  }
//      subject is object
    $subject = (Engine_Api::_()->core()->hasSubject()) ? Engine_Api::_()->core()->getSubject() : null;

    if (!$subject || !$subject->getIdentity()) {
      return $this->setNoRender();
    }

		if ( $subject instanceof Page_Model_Page ){
			if (!in_array('rate', (array) $subject->getAllowedFeatures())){
				return $this->setNoRender();
			}
		}

//      $this->view->item_type = $item_type = 'user';
//      $table = Engine_Api::_()->getDbtable('rates', 'rate');
//      $settings = Engine_Api::_()->getApi('settings', 'core');
//      $this->view->maxRate = $settings->getSetting('rate.' . $item_type . '.max.rate', 5);
//      $this->view->maxRate = 5; // todo change stars count
//      $maxItems = $settings->getSetting('rate.' . $item_type . '.max.items', 5);
//      $minVotes = $settings->getSetting('rate.' . $item_type . '.min.votes', 1);
//      $this->view->period = $period = $settings->getSetting('rate.' . $item_type . '.period_enabled', true);
//      $mostRatedItems = $table->fetchMostRated($item_type, $maxItems, $minVotes);
//
//
//      if (empty($mostRatedItems)) {
//          return $this->setNoRender();
//      }
//
//      if ($period) {
//          $this->view->month_rates = $this->_prepareRates($table->fetchMostRated($item_type, $maxItems, $minVotes, 'month'));
//          $this->view->week_rates = $this->_prepareRates($table->fetchMostRated($item_type, $maxItems, $minVotes, 'week'));
//      }
//      $usersTbl = Engine_Api::_()->getDbTable('users','user');
//      $select = $usersTbl->select()->where('user_id IN (?)', $this->item_ids);
//      $items = $usersTbl->fetchAll($select);
//
//      $table = Engine_Api::_()->getDbtable('rates', 'rate');
//
//      $itemsForView = array();
//foreach($items as $item){
//    $allowOtherLevelsRateToThisLevel = Engine_Api::_()->authorization()->isAllowed('rate', $item, 'rateenabled');
//    $viewer = Engine_Api::_()->user()->getViewer();
//        $rate_info= $table->fetchRateInfo($item_type, $item->getIdentity());
//        $item_score = ($rate_info && $rate_info['rate_count']) ? $rate_info['total_score'] / $rate_info['rate_count'] : 0;
//        $a['item_score']=round($item_score,2);
//        $a['rate_info']=$rate_info;
//         $itemArr = (array)$item;
//            array_push($itemArr,$a);
//         $r = (object)$itemArr;
//
//
//
//            array_push($itemsForView,$item);
//
//
//    }

    $this->view->item_type = $item_type = strtolower($subject->getType());
    $this->view->item_id = $item_id = $subject->getIdentity();

$this->view->isReview = false;

    $allowOtherLevelsRateToThisLevel = Engine_Api::_()->authorization()->isAllowed('rate', $subject, 'rateenabled');
    $viewer = Engine_Api::_()->user()->getViewer();
    if ($subject->getType() == 'user' && !$allowOtherLevelsRateToThisLevel && $viewer->getIdentity() != $subject->getIdentity()) {
      return $this->setNoRender();
    }

    if ($subject->getType() == 'page'){

      $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('page');
      $this->view->pageId = $page_id = $subject->getIdentity();
      $rate_info = Engine_Api::_()->getDbTable('pagereviews', 'rate')->getScore($page_id);
      if (!is_array($rate_info)) {
        return $this->setNoRender();
      }
      if(!$rate_info['item_score'] || $rate_info['item_score']<=0){
        return $this->setNoRender();
      }
      $tbl_vote = Engine_Api::_()->getDbTable('votes', 'rate');
      $select = $tbl_vote->select()
          ->from( $tbl_vote->info('name'), new Zend_Db_Expr("type_id, SUM(rating) AS rating, COUNT(*) AS total") )
          ->where('page_id = ?', $page_id)
          ->group('type_id');
      $ratings = $tbl_vote->getAdapter()->fetchAll($select);


      $rating_list = array();
      foreach ($ratings as $rating){
        $rating_list[$rating['type_id']] = round($rating['rating'] / $rating['total'], 2);
      }
      $types = Engine_Api::_()->getApi('core', 'rate')->getPageTypes($page_id);
      foreach ($types as $key=>$type){
        if (array_key_exists($type->type_id, $rating_list)){
          $types[$key]->value = $rating_list[$type->type_id];
        }
      }
      $this->view->types = $types;
      $this->view->isReview = true;

      if (!count($types)){ return $this->setNoRender(); }

    } else {

        if (!Engine_Api::_()->rate()->isSupportedPlugin($item_type)) {
            return $this->setNoRender();
        }

        $this->view->item = $subject;
        $table = Engine_Api::_()->getDbtable('rates', 'rate');
        $this->view->rate_info = $rate_info = $table->fetchRateInfo($item_type, $item_id);
//        rate_info = Array
//        (
//            [rate_count] => 8
//            [total_score] => 38
//          )
        $item_score = ($rate_info && $rate_info['rate_count']) ? $rate_info['total_score'] / $rate_info['rate_count'] : 0;
        $this->view->assign('item_score', round($item_score, 2));

        //  $settings = Engine_Api::_()->getApi('settings', 'core');
        //  $this->view->maxRate = $settings->getSetting('rate.' . $subject . '.max.rate', 5);
        $this->view->maxRate = 5; // todo edit stars count

        $can_rate = $this->_getParam('can_rate', true);
        $error_msg = $this->_getParam('error_msg', '');

        $front_router = Zend_Controller_Front::getInstance()->getRouter();

        $this->view->assign('rate_url', $front_router->assemble(array('module' => 'rate'), 'widget_rate'));
        $this->view->assign('rate_uid', uniqid('rate_'));
        $this->view->can_rate = Zend_Json::encode(array('can_rate' => $can_rate, 'error_msg' => $error_msg));


    }
  }

}