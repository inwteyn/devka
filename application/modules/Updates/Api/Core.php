<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 02.12.10
 * Time: 15:00
 * To change this template use File | Settings | File Templates.
 */

class Updates_Api_Core extends Core_Api_Abstract
{
	public function getContentItems($params)
	{
		$id = $params['content_id'];
		$displayed = explode(',', $params['displayed']);
		$blacklist = explode(',', $params['blacklist']);

		$displayed = array_unique(array_merge($displayed, $blacklist));

		$displayed_tmp = array();
		foreach($displayed as $key=>$value){
			if(isset($value) && trim($value) !=''){
				$displayed_tmp[] = $value;
			}
		}

    /**
     * @var $contentTb Updates_Model_DbTable_Content
     */

		$displayed = $displayed_tmp;
		$contentTb = Engine_Api::_()->getDbTable('content', 'updates');

		if (null == ($content = $contentTb->getContentWidget($id)))
		{
			$this->_forward('success', 'utility', 'core', array (
				'smoothboxClose' => TRUE,
				'parentRefresh' => FALSE,
				'format'=> 'smoothbox',
				'messages' => Zend_Registry::get('Zend_Translate')->translate('UPDATES_No content found with id') . ' ' . $id,
			));
		}

		$content = $content->toArray();
		$content['authTb'] = $authorizationTb = Engine_Api::_()->getDbTable('allow', 'authorization')->info('name');
		$content['count'] = $content['params']['count'];
		$content['title'] = $content['params']['title'];
		$content['select'] = $content['params']['select'];
		$content['title'] = $content['params']['title'];
		$content['displayed'] = implode(',', $displayed);
		$content['count'] = count($displayed);
		$content['blacklist'] = null;

    $items = $contentTb->getContentData($content['name'], $content);

		return Zend_Paginator::factory($items);
	}

	public function urlsEncode($message, $id = '', $type='updates')
	{
    $view = Zend_Registry::get('Zend_View');
		$host = $_SERVER['HTTP_HOST'] .$view->baseUrl();
		$matches = array();
    $host = str_replace('/', '\/', $host);
  	preg_match_all('/href="http:\/\/' . $host . '+[a-zA-Z0-9\/\-\_\s@\.\$\&\?=!\{\}\:\;\,\%\#\*\(\)\+]*"/', $message, $matches);

  	$originals = array();
  	$encoded = array();

    if ( count($matches) <= 0 ) return $message;

  	foreach ($matches[0] as $match)
		{
			$exploded = explode('"', $match);
			$originals[] = $match;
			$encoded[] = 'target="blank" href="http://' . $host . '/updates/ajax/referred/' . $type . '/' . $id . '/' . str_replace('%', '_enc_', urlencode($exploded[1])) . '"';
		}

		if (isset($originals) && isset($encoded))
		{
			$message = str_replace($originals, $encoded, $message);
		}

 		return $message;
	}

  public function getTimezone()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    if ($settings->__get('core.locale.timezone')){
      return $settings->__get('core.locale.timezone');
    } else {
      return Zend_Registry::get('timezone');
    }
  }

  public function getDatetime($datetime = null)
  {
    $dt = new Zend_Date();
    $dt->setTimezone( $this->getTimezone() );

    if ($datetime != null)
    {
      $dt->setTime($datetime);      
    }

    return $dt->get(Zend_Date::DATETIME);
  }

  public function getTimestamp($datetime = null, $dt = null)
  {
    if (!$dt) {
      $dt = new Zend_Date();
      $dt->setTimezone( $this->getTimezone() );
    }

    if ($datetime != null)
    {
      $dt->setTime($datetime);
    }

    $year   = $dt->get(Zend_Date::YEAR);
    $month  = $dt->get(Zend_Date::MONTH_SHORT);
    $day    = $dt->get(Zend_Date::DAY_SHORT);
    $hour   = $dt->get(Zend_Date::HOUR_SHORT);
    $minute = $dt->get(Zend_Date::MINUTE_SHORT);
    $second = $dt->get(Zend_Date::SECOND_SHORT);

    $ts = mktime($hour, $minute, $second, $month, $day, $year);

    return $ts;
  }
}