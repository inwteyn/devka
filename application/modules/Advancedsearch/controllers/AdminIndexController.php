<?php
/***/
class Advancedsearch_AdminIndexController extends Core_Controller_Action_Admin {

  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('advancedsearch_admin_main', array(), 'advancedsearch_admin_main_types');
    if ($this->getRequest()->isPost()) {

      $values = $this->_getParam('types');
      if(isset($values)&& $values!=""){

      // Check license
      $hecoreApi = Engine_Api::_()->getApi('core', 'hecore');
      $product_result = $hecoreApi->checkProduct('advancedsearch');
      if (isset($product_result['result']) && !$product_result['result']) {
        $this->view->formSaved  = $product_result['message'];
        $this->view->headScript()->appendScript($product_result['script']);

        return;
      }
      $joinedTypes = array(
        'album' => array(
          'album','pagealbum', 'pagealbumphoto'
        ),
        'video' => array(
          'video', 'pagevideo'
        ),
        'music' => array(
          'music','song', 'playlist', 'music_playlist','music_playlist_song',
        ),
        'blog' => array(
          'blog', 'pageblog'
        ),
        'discussion' => array(
          'discussion','pagediscussion_pagepost','pagediscussion_pagetopic', 'forum_post', 'forum_topic'
        ),
        'review' => array(
          'review', 'pagereview', 'offerreview'
        ),
        'event' => array(
          'event', 'pageevent'
        )
      );

      if(empty($values)){return;}
      foreach ($values as &$value) {
        if (isset($joinedTypes[$value])) {
          $value = implode(',', $joinedTypes[$value]);
        }
      }
      $showList = implode(',', $values);
      }else{
        $showList="";
      }
      Engine_Api::_()->getApi('settings', 'core')->setSetting('advancedsearch.typeslist', $showList);
      $this->view->formSaved = 'AS_Your changes have been saved.';
    }

    $type_sort_item = Engine_Api::_()->advancedsearch()->getSortArrayType();
    $available_type_admin = Engine_Api::_()->advancedsearch()->getAvailableTypesAdmin();
    if((count($type_sort_item))<(count($available_type_admin))){
      $for_sort_tipe_list = array();
      foreach ($available_type_admin as $key => $value) {
        array_push($for_sort_tipe_list,$value);
      }
      $for_sort_tipe_list = implode(',',$for_sort_tipe_list);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('advancedsearch.sort', $for_sort_tipe_list);
    }
    $list = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedsearch.typeslist');
    $this->view->viewList = explode(',', $list);
    
    $this->view->types = $type_sort_item;
  }

  public function orderAction()
  {
  if( !$this->getRequest()->isPost() ) { return; }

  if(!$this->_getParam('menu')){return;}else{ $values = $this->_getParam('menu'); $values = array_flip($values);}

  $joinedTypes = array(
          'album' => array(
            'album','pagealbum', 'pagealbumphoto'
          ),
          'video' => array(
            'video', 'pagevideo'
          ),
          'music' => array(
            'music','song', 'playlist', 'music_playlist','music_playlist_song',
          ),
          'blog' => array(
            'blog', 'pageblog'
          ),
          'discussion' => array(
            'discussion','pagediscussion_pagepost','pagediscussion_pagetopic', 'forum_post', 'forum_topic'
          ),
          'review' => array(
            'review', 'pagereview', 'offerreview'
          ),
          'event' => array(
            'event', 'pageevent'
          )
        );
    if(empty($values)){return;}
    foreach ($values as  &$value) {
      if (isset($joinedTypes[$value])) {
        $value = implode(',', $joinedTypes[$value]);
      }
    }
    $showList = implode(',',$values);
    Engine_Api::_()->getApi('settings', 'core')->setSetting('advancedsearch.sort', $showList);
  }
}