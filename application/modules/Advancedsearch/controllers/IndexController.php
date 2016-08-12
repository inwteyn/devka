<?php

class Advancedsearch_IndexController extends Core_Controller_Action_Standard
{
  protected $_childCount;

  public function getChildCount()
  {
     return $this->_childCount;
  }

  public function indexAction()
  {
    $this->view->form = $form = new Advancedsearch_Form_Search();



    $string_for_array_mini_menu = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedsearch.typeslist');
    $mini_menu_array = explode(',',$string_for_array_mini_menu);
    $this->view->types = $mini_menu_array;
   // $this->view->types = $types = Engine_Api::_()->advancedsearch()->getAvailableTypes();

    if ($this->_getParam('squery')) {
      $this->view->squery = $this->_getParam('squery');
    }
    if ($this->_getParam('stype')) {
      $this->view->stype = $this->_getParam('stype');
    }
  }

  public function searchAction()
  {

    $types = array(
      'album' => array(
        'album','pagealbum', 'pagealbumphoto', 'album_photo'
      ),
      'video' => array(
        'video', 'pagevideo'
      ),
      'music' => array(
        'song', 'playlist','music_playlist','music_playlist_song'
      ),
      'blog' => array(
        'blog', 'pageblog'
      ),
      'discussion' => array(
        'pagediscussion_pagepost','pagediscussion_pagetopic', 'forum_post', 'forum_topic'
      ),
      'review' => array(
        'pagereview', 'offerreview'
      ),
      'event' => array(
        'event', 'pageevent'
      )

    );

    $this->view->query = $text = $this->_getParam('query');
    $this->view->stype = $type = $this->_getParam('type');
    $page = intval($this->_getParam('page'));

    if (isset($types[$type])) {
      $type = $types[$type];
    }
    if ($this->_getParam('global')) {

      $this->view->global = true;


      $itemscount = Engine_Api::_()->advancedsearch()->getSelectCount($text,$type);

      $items = Engine_Api::_()->advancedsearch()->getGlobalResult($text, $type);

      $this->view->countItem = $itemscount;

      $type_ids = array();

      foreach ($items as $key => $item) {
        $type_ids[$item['type']][] = $item['id'];
      }

      $tempTypeModels = array();

      foreach($type_ids as $type => $ids)
      {
        $table = Engine_Api::_()->getItemTable($type);
        $primary = $table->info(Zend_Db_Table_Abstract::PRIMARY);
        $primary = $primary[1];
        $tempTypeModels[$type] = $table->fetchAll($table->select()->where($primary . ' IN (?)', $ids));
      }
      $setting_type = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedsearch.typeslist');
      $setting_type = explode(',',$setting_type);
      $this->view->count_row_tipe = count($setting_type);
      $this->view->name_category = $setting_type;
      $this->view->items = $tempTypeModels;
      $this->view->countItem = Engine_Api::_()->advancedsearch()->getSelectCountAll($text);
      } else {
      $this->view->items = $items = Engine_Api::_()->advancedsearch()->getPaginator($text, $type, $page);

    }
    $this->view->html = $this->view->render('search.tpl');
  }
}
