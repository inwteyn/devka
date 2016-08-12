<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Updates.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
  
class Updates_Model_DbTable_Updates extends Engine_Db_Table
{
  protected $_rowClass = "Updates_Model_Update";

	public $_last_sent_id;

  public function getReferreds($date = 0)
  {
  	if ($date)
  	{
	 	  $selectUpates = $this->select()
				->setIntegrityCheck(false)
				->from($this->info('name'), array('sum(referred) AS total_referreds', 'GROUP_CONCAT(update_id) AS update_ids'))
				->where('creation_date > ?', $date)->limit(1);
  	}
  	else 
  	{
	 	  $selectUpates = $this->select()
	   		->setIntegrityCheck(false)
	   		->from($this->info('name'), array('sum(referred) AS total_referreds'))->limit(1);
  	}
  	
		return $this->fetchRow($selectUpates);
  }
  
	public function getInsertId()
  {
		$status = $this->_db->query("SHOW TABLE STATUS LIKE '".$this->info('name'). "' ")->fetch();
		return $status['Auto_increment'];
  }

	//@return Updates_Model_Updates
	public function getUpdate($update_id)
	{
		if (is_numeric($update_id) && $update_id>0)
			return $this->fetchRow($this->select()->where('update_id=?', $update_id)->limit(1));
		else
			return false;
	}

	public function getSendingUpdate()
	{
		$select = $this->select()
			->where('sending_finished = 0')
			->order('creation_date DESC')
			->limit(1);

		return $this->fetchRow($select); 
	}

	public function stopSendingUpdate()
	{
		return $this->update(array('sending_finished'=>1), '');
	}

  /**
   * @param array $params
   * @return bool|Updates_Model_Updates|Zend_Db_Table_Row_Abstract
   */

	public function prepareUpdate($params = array(), $check_active_update = false)
  {
    /**
     * Prepare Variables
     *
     * @var $authorizationTb Authorization_Model_DbTable_Allow
     * @var $contentTb Updates_Model_DbTable_Content
     * @var $view Zend_View
     * @var $log Zend_Log
     * @var $row Updates_Model_Updates
     */
    $default = 0;
		$authorizationTb = Engine_Api::_()->getDbtable('allow', 'authorization');
		$contentTb = Engine_Api::_()->getDbtable('content', 'updates');
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $view = Zend_Registry::get('Zend_View');
    $log = Zend_Registry::get('Zend_Log');

    if ($check_active_update) {
      $updateSelect = $this->select()
        ->where('sending_finished = ?', 0)
        ->where('sending_cancelled = ?', 0)
        ->where('creation_date BETWEEN SUBDATE(NOW(), INTERVAL ? DAY) AND NOW()', 8)
        ->order('update_id ASC')
        ->limit(1);

      $updateRow = $this->fetchRow($updateSelect);

      if ($updateRow) {
        return $updateRow;
      }
    }

    $row = $this->createRow();
    $row->update_id = $this->getInsertId();
    $translate    = Zend_Registry::get('Zend_Translate');
    $languageList = $translate->getList();
    $origin = $translate->getLocale();
    // Get included contents/widgets
    foreach($languageList as $key => $lang) {


      try {
        $language = Zend_Locale::findLocale($lang);
      } catch( Exception $e ) {
        $language = $lang;
      }
      $localeObject = new Zend_Locale($language);
      Zend_Registry::set('Locale', $localeObject);
      $contents = $contentTb->getContentWidgets();
      setcookie('en4_language', $language, time() + (86400 * 365), '/');
      setcookie('en4_locale', $language, time() + (86400 * 365), '/');

      try{
        $translate->setLocale($language);
      }catch (Exception $e){
        print_die($e.'');
      }


      $updates = array();
      $isExistWidgetData = array();

      foreach ($contents as $content) {
        switch ($content['name']) {
          case 'notifications':
            $updates[$content['id']] = '[notifications]';
            break;

          case 'htmlblock':
            $updates[$content['id']] = $content['html'];
            break;

          default:

            try {


              $content['authTb'] = $authorizationTb->info('name');
              $update = $contentTb->getContentData($content['name'], $content);

              // Unite new_albums and new_albums_page
              if ($content['name'] == 'new_albums' && $update->count() > 0) {
                $isExistWidgetData['new_albums'] = 1;
              }
              if ($content['name'] == 'new_albums_page' && $update->count() > 0 && !$isExistWidgetData['new_albums']) {
                $content['parent_title'] = $translate->translate('New Albums');
              }

              // Unite new_events and new_events_page
              if ($content['name'] == 'new_events' && $update->count() > 0) {
                $isExistWidgetData['new_events'] = 1;
              }
              if ($content['name'] == 'new_events_page' && $update->count() > 0 && !$isExistWidgetData['new_events']) {
                $content['parent_title'] = $translate->translate('New Events');
              }

              // Unite new_videos and new_videos_page
              if ($content['name'] == 'new_videos' && $update->count() > 0) {
                $isExistWidgetData['new_videos'] = 1;
              }
              if ($content['name'] == 'new_videos_page' && $update->count() > 0 && !$isExistWidgetData['new_videos']) {
                $content['parent_title'] = $translate->translate('New Videos');
              }

              // Unite new_blogs and new_blogs_page
              if ($content['name'] == 'new_blogs' && $update->count() > 0) {
                $isExistWidgetData['new_blogs'] = 1;
              }
              if ($content['name'] == 'new_blogs_page' && $update->count() > 0 && !$isExistWidgetData['new_blogs']) {
                $content['parent_title'] = $translate->translate('New Blogs');
              }

              // Unite new_playlists and new_playlists_page
              if ($content['name'] == 'new_playlists' && $update->count() > 0) {
                $isExistWidgetData['new_playlists'] = 1;
              }
              if ($content['name'] == 'new_playlists_page' && $update->count() > 0 && !$isExistWidgetData['new_playlists']) {
                $content['parent_title'] = $translate->translate('New Playlists');
              }

              $updateHTML = '';
              if ($update instanceof Engine_Db_Table_Rowset && $update->count() > 0) {
                $this->_last_sent_id[$content['name']] = 0;
                $updateHTML = $view->widgetHTML($content, $update, $params);

                if ($view->baseUrl()) {
                  $updateHTML = str_replace(
                    array('src="/application/', "src='/application/"),
                    array(
                      'src="' . $view->baseUrl() . '/application/',
                      "src='" . $view->baseUrl() . '/application/',
                    ),
                    $updateHTML);
                }

                if (@!$params['preview'] && @!$params['testemail']) {
                  if ($view->baseUrl()) {
                    $updateHTML = str_replace(
                      array('href="' . $view->baseUrl(), "href='" . $view->baseUrl()),
                      array(
                        'href="',
                        "href='",
                      ),
                      $updateHTML);
                  }

                  $updateHTML = str_replace(
                    array('href="/', "href='/", 'href="http:///', "href='http:///", 'src="/', "src='/", 'src="application/', "src='application/"),
                    array(
                      'target="_blank" href="http://' . $_SERVER['HTTP_HOST'] . $view->baseUrl() . '/module0' . $content['module'] . '/',
                      "target='_blank' href='http://" . $_SERVER['HTTP_HOST'] . $view->baseUrl() . '/module0' . $content['module'] . '/',
                      'target="_blank" href="http://' . $_SERVER['HTTP_HOST'] . $view->baseUrl() . '/module0' . $content['module'] . '/',
                      "target='_blank' href='http://" . $_SERVER['HTTP_HOST'] . $view->baseUrl() . '/module0' . $content['module'] . '/',
                      'src="http://' . $_SERVER['HTTP_HOST'] . '/',
                      "src='http://" . $_SERVER['HTTP_HOST'] . '/',
                      'src="http://' . $_SERVER['HTTP_HOST'] . $view->baseUrl() . '/application/',
                      "src='http://" . $_SERVER['HTTP_HOST'] . $view->baseUrl() . '/application/',
                    ),
                    $updateHTML);
                } else {
                  $updateHTML = str_replace(
                    array('href="/', "href='/", 'href="http:///', "href='http:///", 'src="/', "src='/", 'src="application/', "src='application/"),
                    array(
                      'target="_blank" class="msgLink" href="http://' . $_SERVER['HTTP_HOST'] . '/',
                      "target='_blank' class='msgLink' href='http://" . $_SERVER['HTTP_HOST'] . '/',
                      'href="http://' . $_SERVER['HTTP_HOST'] . '/',
                      "href='http://" . $_SERVER['HTTP_HOST'] . '/',
                      'src="http://' . $_SERVER['HTTP_HOST'] . '/',
                      "src='http://" . $_SERVER['HTTP_HOST'] . '/',
                      'src="http://' . $_SERVER['HTTP_HOST'] . '/application/',
                      "src='http://" . $_SERVER['HTTP_HOST'] . '/application/',
                    ),
                    $updateHTML);
                }

                $update_exists = true;
                $updates[$content['id']] = $updateHTML;
              }
            } catch (Exception $e) {
              print_log($e);
            }

            break;
        }
      }

      if (isset($params['preview']) || isset($params['testemail']) || (isset($update_exists))) {
        $contentTable = Engine_Api::_()->getDbtable('content', 'updates');
        $contentRowset = $contentTable->fetchAll($contentTable->select()->order(array('order ASC', 'name ASC')));
        $contentStructure = $contentTable->prepareContentStructure($contentRowset);

        $contentAreas = array();

        foreach ($contentStructure as $containers) {
          $contentAreas[$containers['name']] = array();

          foreach ($containers['elements'] as $container) {
            $contentAreas[$containers['name']][$container['name']] = '';

            foreach ($container['elements'] as $widget) {
              if (array_key_exists($widget['id'], $updates)) {
                $contentAreas[$containers['name']][$container['name']] .= $updates[$widget['id']];
              }
            }

            if ($contentAreas[$containers['name']][$container['name']] == '') {
              unset($contentAreas[$containers['name']][$container['name']]);
            }
          }
        }

        $row->message = $view->message($contentAreas);

        //IF NOT PREVIEW OR TEST EMAIL THEN SAVE MESSAGE
        if (!isset($params['preview']) && !isset($params['testemail'])) {
          $message = Engine_Api::_()->updates()->urlsEncode($row->message, $row->update_id);
          $message = str_replace('\\', '', $message);

          $message .= "<img src='http://" . $_SERVER['HTTP_HOST'] . $view->baseUrl() . "/updates/ajax/image/updates/" . $row->update_id . "' border='0'/>";

          $remove = array("\n", "\r\n", "\r");
          $message = str_replace($remove, ' ', $message);

          $remove = array("    ", "   ", "  ");

          $row->message = str_replace($remove, " ", $message);
          $row->message = htmlspecialchars($row->message);
          $tableMessage = Engine_Api::_()->getDbTable('messages','updates');
          $Mrow = $tableMessage->createRow();
          $Mrow->update_id = $row->update_id;
          $Mrow->message = $row->message;
          if('en' == $lang || 'en_EN' == $lang|| 'EN_en' == $lang){
            $default = $row->message;
          }
          $Mrow->lang = $lang;
          $Mrow->save();

          $widgetTb = Engine_Api::_()->getDbtable('widgets', 'updates');
          $widgets = $widgetTb->fetchAll($widgetTb->select());
          foreach ($widgets as $widget) {
            if (isset($this->_last_sent_id[$widget->name]) &&
              ($widget->last_sent_id < $this->_last_sent_id[$widget->name])
            ) {
              $widget->last_sent_id = $this->_last_sent_id[$widget->name];
              $widget->save();
            }
          }
        }


      }
    }
    try {
      $language = Zend_Locale::findLocale($origin);
    } catch( Exception $e ) {
      $language = $lang;
    }
    $localeObject = new Zend_Locale($language);
    Zend_Registry::set('Locale', $localeObject);
    $contents = $contentTb->getContentWidgets();
    setcookie('en4_language', $language, time() + (86400 * 365), '/');
    setcookie('en4_locale', $language, time() + (86400 * 365), '/');

    try{
      $translate->setLocale($language);
    }catch (Exception $e){
      print_die($e.'');
    }
    if($default){
      $row->message = $default;
    }
    $row->save();
    if($row){
      return $row;
    }

 		return false;
  }

}