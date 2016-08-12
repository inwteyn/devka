<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Bootstrap.php 2010-07-02 19:52 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Hecore_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application)
  {
    $this->initViewHelperPath();
    parent::__construct($application);
//    $log = Zend_Registry::get('Zend_Log');
//      $log->addWriter(new Hecore_Plugin_CrashLog(Engine_Db_Table::getDefaultAdapter(), 'engine4_hecore_log'));
  }
}

if (!function_exists('print_arr')) {
  function print_arr($var, $return = false, $line = false)
  {

    if (!$line) {
      $bt = debug_backtrace();
      $caller = array_shift($bt);
      $line = 'File: ' . $caller['file'] . ' line:  ' . $caller['line'];
    }
    $type = gettype($var);

    $out = print_r($var, true);
    $out = htmlspecialchars($out);
    $out = str_replace(' ', '&nbsp; ', $out);
    if ($type == 'boolean')
      $content = $var ? 'true' : 'false';
    else
      $content = nl2br($out);

    $out = '<div style="
       border:2px inset #666;
       background:black;
       font-family:Verdana;
       font-size:11px;
       color:#6F6;
       text-align:left;
       margin:20px;
       padding:16px">
         <span style="color: paleturquoise"><p>Calling in ' . $line . '</p></span><br><span style="color: #F66">(' . $type . ')</span> ' . $content . '</div><br /><br />';

    if (!$return)
      echo $out;
    else
      return $out;
  }
}

if (!function_exists('print_die')) {
  function print_die($var, $return = false, $ip = null)
  {
    if (($ip && $_SERVER['REMOTE_ADDR'] == $ip) || !$ip) {
      $bt = debug_backtrace();
      $caller = array_shift($bt);

      print_arr($var, $return, 'File: ' . $caller['file'] . ' line:  ' . $caller['line']);
      //print_arr($var, $return);
      die;
    }
  }
}

if (!function_exists('print_log')) {
  function print_log($str)
  {
    if (!is_string($str)) {
      $str = print_r($str, true);
    }

    $log = new Zend_Log();
    $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/hecore_log.log'));
    $log->log($str . "\n\r\n\r", Zend_Log::INFO);
  }
}

if (!function_exists('print_firebug')) {
  function print_firebug($str)
  {
    $log = new Zend_Log();
    $log->addWriter(new Zend_Log_Writer_Firebug());
    $log->log($str, Zend_Log::INFO);
  }
}

if (!function_exists('print_slack')) {
  function print_slack($var)
  {
    $channel = 'https://hooks.slack.com/services/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';
    $message = 'DEV:' . (is_string($var) ? $var : print_r($var, true));
    $data = array(
      'channel' => '#he-debug',
      'username' => 'Taskflowy',
      'text' => $message,
      'icon_url' => 'http://pbs.twimg.com/profile_images/3466225963/b844139b08cb9903dbd3b0b90f4d4af8_normal.png'
    );
    $data_string = json_encode($data);

    $ch = curl_init($channel);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string))
    );
    //Execute CURL
    $result = curl_exec($ch);
    return $result == 0;
  }
}

//print_slack('SuperStas the owner of the planet earth!!!');