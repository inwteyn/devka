<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ItemRate.php 2010-07-02 19:53 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Advancedsearch_View_Helper_ItemSearch extends Engine_View_Helper_HtmlElement
{
  public function itemSearch($item)
  {
    $method = '_' . $item->getType() . 'Item';
    if(method_exists($this, $method)) {
      return $this->$method($item);
    }
  }

  private function _pageItem($item)
  { if(Engine_Api::_()->hasModuleBootstrap('like')){
    $block_view = '<div class="as_global_search_option">' .$this->view->translate("AS_like_count").": ".$item->getLikesCount(). '</div>';
    return $block_view;
    }
  }

  private function _donationItem($item)
  {
    $block_view = '<div class="as_global_search_option">' .$this->view->translate("AS_raised").": ".$item->getRaised(). '</div>';
       return $block_view;
  }
  private function _eventItem($item)
  {
    $block_view = '<div class="as_global_search_option"><i class="hei hei-clock-o"></i>'. $item->starttime. '</div>';
       return $block_view;
  }
  private function _albumItem($item)
  {
    $block_view = '<div class="as_global_search_option">' .$this->view->translate("AS_count_album").": ".$item->count(). '</div>';
       return $block_view;
  }

  private function _pagealbumItem($item){

    $block_view = '<div class="as_global_search_option">' .$this->view->translate("AS_count_album").": ".$item->count(). '</div>';
       return $block_view;
  }

  private function _groupItem($item){
    $block_view = '<div class="as_global_search_option">' .$this->view->translate("AS_members_count").": ".$item->member_count. '</div>';
       return $block_view;
  }

  private function _hecontestItem($item){
    $block_view = '<div class="as_global_search_option">' .$this->view->translate("AS_participants").": ".$item->getParticipantsCount(). '</div>';
       return $block_view;
  }

  private function _hequestionItem($item){
    $block_view = '<div class="as_global_search_option">' .$this->view->translate("AS_vote").": ".$item->vote_count. '</div>';
       return $block_view;
  }

  private function _quizItem($item){
    $block_view = '<div class="as_global_search_option">' .$this->view->translate("AS_take").": ".$item->take_count. '</div>';
       return $block_view;
  }

  private function _store_productItem($item){
    $block_view = '<div class="as_global_search_option">' .$this->view->translate("AS_price").": ".$item->getPrice($item). '</div>';
       return $block_view;
  }

}