<?php

/**
 * Created by PhpStorm.
 * User: Ulan
 * Date: 27.10.14
 * Time: 20:42
 */
class Hecore_Plugin_CrashLog extends Zend_Log_Writer_Db
{
  private $_plugins = array(
    'Advancedsearch',
    'Appmanager',
    'Apptablet',
    'Apptouch',
    'Checkin',
    'Credit',
    'Daylogo',
    'Donation',
    'Hashtag',
    'Headvancedalbum',
    'Hebadge',
    'Hecontest',
    'Hecore',
    'Heevent',
    'Hegift',
    'Heloginpopup',
    'Hequestion',
    'Hetips',
    'Highlights',
    'Inviter',
    'Like',
    'Mobile',
    'Offers',
    'Page',
    'Pagealbum',
    'Pageblog',
    'Pagecontact',
    'Pagediscussion',
    'Pagedocument',
    'Pageevent',
    'Pagefaq',
    'Pagemusic',
    'Pagevideo',
    'Photoviewer',
    'Pinfeed',
    'Quiz',
    'Rate',
    'Store',
    'Suggest',
    'Timeline',
    'Touch',
    'Updates',
    'Usernotes',
    'Wall',
    'Weather',
    'Welcome',
  );

  protected function _write($event)
  {
    $message = $event['message'];
    if (!preg_match('/' . implode('|', $this->_plugins) . '/i', $message)) return;
    if ($this->_db === null) {
      // require_once 'Zend/Log/Exception.php';
      throw new Zend_Log_Exception('Database adapter is null');
    }


    if ($this->_columnMap === null) {
      $dataToInsert = $event;
    } else {
      $dataToInsert = array();
      foreach ($this->_columnMap as $columnName => $fieldKey) {
        if (isset($event[$fieldKey])) {
          $dataToInsert[$columnName] = $event[$fieldKey];
        }
      }
    }
    $plugin = '';
    foreach ($this->_plugins as $plugin)
      if (strpos($message, $plugin)) break;
    $dataToInsert['plugin'] = strtolower($plugin);
    unset($dataToInsert['domain']);
    if ($user_id = Engine_Api::_()->user()->getViewer()->getIdentity())
      $dataToInsert['user_id'] = $user_id;
    $tmp = explode("\nError Code: ", $dataToInsert['message']);
//      print_die($event);
    $mess = $tmp[0];
    $trace = $tmp[1];
    if (count($tmp) < 2) {
      $trace = $tmp[0];
      $mess = '';
    }
    if (!$mess) {
      $start = strpos($trace, " with message ") + 14;
      $mess = substr($trace, $start, strpos($trace, "\nStack trace:\n") - $start);
    }
    $dataToInsert['message'] = $mess;
    if(count($tmp) == 2)
      $dataToInsert['error_code'] = substr($tmp[1], 0, 6);
    $dataToInsert['trace'] = substr($trace, strpos($trace, "\nStack trace:\n") + 14);
//    print_die($dataToInsert);
    $dataToInsert['url'] = $this->fullUrl();
    $this->_db->insert($this->_table, $dataToInsert);
    $last_log_id = $this->_db->lastInsertId();
    $reportCountPer = 50;
    if($last_log_id % $reportCountPer == 0){
      print_die(Zend_Json::encode($this->_db->select()
            ->from('engine4_hecore_log')
            ->where('`log_id` > ?', $last_log_id  - $reportCountPer)
            ->query()
            ->fetchAll()));
    }
  }

  private function urlOrigin($s, $use_forwarded_host=false)
  {
      $ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true:false;
      $sp = strtolower($s['SERVER_PROTOCOL']);
      $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
      $port = $s['SERVER_PORT'];
      $port = ((!$ssl && $port=='80') || ($ssl && $port=='443')) ? '' : ':'.$port;
      $host = ($use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
      $host = isset($host) ? $host : $s['SERVER_NAME'] . $port;
      return $protocol . '://' . $host;
  }
  private function fullUrl()
  {
    $s = $_SERVER;
      return $this->urlOrigin($s) . $s['REQUEST_URI'];
  }
} 