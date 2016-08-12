<?php
/***/
class Advancedsearch_Api_Core extends Core_Api_Abstract
{
  public function getAvailableTypes()
  {
    $types = Engine_Api::_()->getDbtable('search', 'core')->getAdapter()
      ->query('SELECT DISTINCT `type` FROM `engine4_core_search`')
      ->fetchAll(Zend_Db::FETCH_COLUMN);
      $coreModule = Engine_Api::_()->getDbTable('modules', 'core');
    $types = array_intersect($types, Engine_Api::_()->getItemTypes());
    if ($coreModule->isModuleEnabled('pagediscussion') || $coreModule->isModuleEnabled('forum')) {
      $types['discussion'] = 'discussion';
    }

    if ($coreModule->isModuleEnabled('pagemusic') || $coreModule->isModuleEnabled('music')) {
      $types['music'] = 'music';
    }

    if ($coreModule->isModuleEnabled('rate') && ($coreModule->isModuleEnabled('page') || $coreModule->isModuleEnabled('offer'))) {
      $types['review'] = 'review';
    }

    if (!$coreModule->isModuleEnabled('blog') && $coreModule->isModuleEnabled('pageblog')) {
      $types['blog'] = 'blog';
    }

    if (!$coreModule->isModuleEnabled('video') && $coreModule->isModuleEnabled('pagevideo')) {
      $types['video'] = 'video';
    }
    if (!$coreModule->isModuleEnabled('event') && $coreModule->isModuleEnabled('pageevent')) {
      $types['event'] = 'event';
    }
    if (!$coreModule->isModuleEnabled('album') && $coreModule->isModuleEnabled('pagealbum')) {
      $types['album'] = 'album';
    }
    if (!$coreModule->isModuleEnabled('store') && $coreModule->isModuleEnabled('store_product')) {
         $types['store'] = 'store';
    }
    if (!$coreModule->isModuleEnabled('hebadge') && $coreModule->isModuleEnabled('hebadge')) {
             $types['hebadge'] = 'hebadge';
    }

    $list = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedsearch.typeslist');
    $viewList = explode(',', $list);
    $types = array_intersect($types, $viewList);
    $excludeList = array(
      'music_playlist','pagereview', 'offerreview','music_playlist_song', 'pagealbum', 'pagealbumphoto', 'album_photo', 'pagevideo', 'song', 'playlist', 'pageblog', 'pagediscussion_pagepost', 'pagediscussion_pagetopic', 'pageevent', 'forum_post', 'forum_topic'
    );

    if (!$coreModule->isModuleEnabled('page')) {
      $excludeList[] = 'pagereview';
    }
    foreach ($types as $key => &$type) {
      if (in_array($type, $excludeList)) {
        unset($types[$key]);
      }
    }
    return $types;
  }

  public function getTypeAdmin(){
    $table = Engine_Api::_()->getDbtable('settings', 'core');
    $db = $table->getAdapter();
    $select = $table->select();
    $all_types = "advancedsearch.typeslist";
    $select->where("`name` =?",$all_types);
    $select = $db->fetchAll($select);
    return $select;
  }

  public function getTypeSortAdmin(){
      $table = Engine_Api::_()->getDbtable('settings', 'core');
      $db = $table->getAdapter();
      $select = $table->select();
      $all_types = "advancedsearch.sort";
      $select->where("`name` =?",$all_types);
      $select = $db->fetchAll($select);
      return $select;
    }

  public function getSortArrayType(){

    $first_array = $this->getTypeSortAdmin();
    $first_array = explode(',', $first_array[0]['value']);
    $second_arra = array_flip($this->getAvailableTypesAdmin());

    $tru_array = array();
    foreach($first_array as $value){
      if(isset($second_arra[$value])){
        array_push($tru_array, $value);
      }
    }

    return $tru_array;
  }

  public function getAvailableTypesAdmin()
  {
    $types = Engine_Api::_()->getDbtable('search', 'core')->getAdapter()
      ->query('SELECT DISTINCT `type` FROM `engine4_core_search`')
      ->fetchAll(Zend_Db::FETCH_COLUMN);
    $types = array_intersect($types, Engine_Api::_()->getItemTypes());
      $coreModule = Engine_Api::_()->getDbTable('modules', 'core');
    $excludeList = array(
      'pagereview', 'offerreview','music_playlist','music_playlist_song', 'pagealbum', 'pagealbumphoto', 'album_photo', 'pagevideo', 'song', 'playlist', 'pageblog', 'pagediscussion_pagepost', 'pagediscussion_pagetopic', 'pageevent', 'forum_post','quiz_result','hebadge_creditbadge','hebadge_badge','classified_album','classified_photo', 'store_video', 'forum_topic'
    );
    if ($coreModule->isModuleEnabled('pagediscussion') || $coreModule->isModuleEnabled('forum')) {
      $types['discussion'] = 'discussion';
    }

    if ($coreModule->isModuleEnabled('rate') && ($coreModule->isModuleEnabled('page') || $coreModule->isModuleEnabled('offer'))) {
      $types['review'] = 'review';
    }

    if ($coreModule->isModuleEnabled('pagemusic') || $coreModule->isModuleEnabled('music')) {
      $types['music'] = 'music';
    }

    if (!$coreModule->isModuleEnabled('blog') && $coreModule->isModuleEnabled('pageblog')) {
      $types['blog'] = 'blog';
    }

    if (!$coreModule->isModuleEnabled('video') && $coreModule->isModuleEnabled('pagevideo')) {
      $types['video'] = 'video';
    }
    if (!$coreModule->isModuleEnabled('event') && $coreModule->isModuleEnabled('pageevent')) {
      $types['event'] = 'event';
    }
    if (!$coreModule->isModuleEnabled('album') && $coreModule->isModuleEnabled('pagealbum')) {
      $types['album'] = 'album';
    }
    if (!$coreModule->isModuleEnabled('store') && $coreModule->isModuleEnabled('store_product')) {
         $types['store'] = 'store';
    }
    if (!$coreModule->isModuleEnabled('hecontest') && $coreModule->isModuleEnabled('hecontest')) {
         $types['hecontest'] = 'hecontest';
    }

    foreach ($types as $key => &$type) {
      if (in_array($type, $excludeList)) {
        unset($types[$key]);
      }
    }
    return $types;
  }

  public function getPaginator($text, $type = null, $page = false)
  {
    $paginator = Zend_Paginator::factory($this->getSelect($text, $type, $page));
    if ($page) {
      $paginator->setCurrentPageNumber($page);
    }
    $paginator->setItemCountPerPage(10);
    if ($paginator->getTotalItemCount() <= 10 * ($page - 1))
      return false;
    return $paginator;
  }

  public function getSelect($text, $type = null, $page = 1)
  {
    // Build base query
    $table = Engine_Api::_()->getDbtable('search', 'core');
    $db = $table->getAdapter();
    $select = $table->select();

    $availableTypes = Engine_Api::_()->getItemTypes();

    if ($type && in_array($type, $availableTypes)) {
      $select->where('type = ?', $type);
    } else if (is_array($type)) {
      $select->where('type IN(?)', $type);
    } else {
      $settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedsearch.typeslist');
      $select->where('type IN(?)', explode(',', $settings));
    }
    $select->where("(`title` LIKE  '%$text%' OR `description` LIKE  '%$text%' OR `keywords` LIKE  '%$text%' OR `hidden` LIKE  '%$text%')");
    $select->order(new Zend_Db_Expr($db->quoteInto('MATCH(`title`, `description`, `keywords`, `hidden`) AGAINST (?) DESC', $text)));

    return $select;
  }

  public function getSelectCount($text,$type)
    {
      $table = Engine_Api::_()->getDbtable('search', 'core');
      $db = $table->getAdapter();
      $select = $table->select();

      if(is_array($type)){
        foreach ($type as  $val ){
          $val_type[]= "'".$val."'";
        }
        $select->where("(`title` LIKE  '%$text%' OR `description` LIKE  '%$text%' OR `keywords` LIKE  '%$text%' OR `hidden` LIKE  '%$text%') AND `type` IN(".implode(',',$val_type).") ");
      }else{
        $select->where("(`title` LIKE  '%$text%' OR `description` LIKE  '%$text%' OR `keywords` LIKE  '%$text%' OR `hidden` LIKE  '%$text%') AND `type`='$type' ");
      }
      $select = $db->fetchAll($select);
      $countItems = count($select);
      return $countItems;
    }

  public function getSelectCountAll($text)
  {
    $table = Engine_Api::_()->getDbtable('search', 'core');
    $db = $table->getAdapter();
    $select = $table->select();
    $select->where("(`title` LIKE  '%$text%' OR `description` LIKE  '%$text%' OR `keywords` LIKE  '%$text%' OR `hidden` LIKE  '%$text%')");
    $select = $db->fetchAll($select);
    $countItems = count($select);
    return $countItems;
  }

  public function getSelectGlobal($text, $type = null, $page = 1)
  {
    $table = Engine_Api::_()->getDbtable('search', 'core');
    $db = $table->getAdapter();
    $select = $table->select();
    $availableTypes = Engine_Api::_()->getItemTypes();
    if ($type && in_array($type, $availableTypes)) {
      $select->where('type = ?', $type);
      $select->where("(`title` LIKE  '%$text%' OR `description` LIKE  '%$text%' OR `keywords` LIKE  '%$text%' OR `hidden` LIKE  '%$text%')");
      $select ->order(new Zend_Db_Expr($db->quoteInto('MATCH(`title`, `description`, `keywords`, `hidden`) AGAINST (?) DESC', $text)))
           ->limit(4, ($page - 1) * 4);
    } else if (is_array($type)) {
      $select->where('type IN(?)', $type);
      $select->where("(`title` LIKE  '%$text%' OR `description` LIKE  '%$text%' OR `keywords` LIKE  '%$text%' OR `hidden` LIKE  '%$text%')");
      $select ->order(new Zend_Db_Expr($db->quoteInto('MATCH(`title`, `description`, `keywords`, `hidden`) AGAINST (?) DESC', $text)))
           ->limit(4, ($page - 1) * 4);
    } else {
      $settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedsearch.typeslist');
      $settings = explode(',', $settings);

      $subqueries = array();

      for($i=0;$i<count($settings);$i++){
        $subselect = $table->select();
        $subselect = $subselect->where('type IN(?)', $settings[$i]).'';
        $subqueries[$i] = $subselect." AND (`title` LIKE  '%$text%' OR `description` LIKE  '%$text%' OR `keywords` LIKE  '%$text%' OR `hidden` LIKE  '%$text%')LIMIT 5";
      }
      $subselect = $table->select();
      $query = count($subqueries)+1;
      $subqueries[$query]= $subselect->where("type  IN('fake_type')").''.'LIMIT  10000';
      $select = $select->union($subqueries)."";
    }
    return $db->fetchAll($select);
  }

  public function getGlobalResult($text, $type)
  {
    $results = $this->getSelectGlobal($text, $type);
      return $results;
  }

  public function deleteItem($type, $id)
  {
    if ($type !='' && intval($id) > 0) {
      $table = Engine_Api::_()->getDbtable('search', 'core');
      $table->delete("type='$type' AND id=$id");
    }
  }
}