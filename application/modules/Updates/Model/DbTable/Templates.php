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

class Updates_Model_DbTable_Templates extends Engine_Db_Table
{
  protected $_rowClass = "Updates_Model_Template";

  public function getTemplate($id)
  {
    return $this->fetchRow($this->select()->where('template_id=?', $id));
  }

  public function getTemplates()
  {
    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from($this->info('name'), array('template_id', 'subject', 'description', 'creation_date'))
      ->order('creation_date DESC');

    return $this->fetchAll($select);
  }

  public function getStandardVariables($recipient)
  {
    $view = Zend_Registry::get('Zend_View');
    $mail = Engine_Api::_()->getApi('settings', 'core')->core_mail;

    $vars['[site_url]'] = 'http://'.$_SERVER['HTTP_HOST'].$view->baseUrl();
    $vars['[notifications]'] = '';
    $vars['[profile_url]'] = $vars['[site_url]'];
    $vars['[contact_url]'] = 'mailto:' . $mail['from'] . '?subject=' . $mail['name'] . ':' . $view->translate('UPDATES_about Newsletter Updates Plugin');
    $vars['[displayname]'] = '';
    $vars['[email]'] = '';
    $vars['[unsubscribe_url]'] = '';


    if ($recipient instanceof User_Model_User)
    {
      $vars['[displayname]'] = $recipient->getTitle();
      $vars['[email]'] = $recipient->email;
      $vars['[unsubscribe_url]'] = $vars['[site_url]'].'/updates/ajax/unsubscribe/'.$recipient->email;

      if( Engine_Api::_()->getDbtable('notifications', 'activity')->hasNotifications($recipient))
      {
        $widget = Engine_Api::_()->getDbtable('widgets', 'updates')->getWidget(array('name'=>'notifications'))->toArray();
        $widget['title'] = $widget['params']['title'];

        $notifications = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationsPaginator($recipient);
        $notifications->setCurrentPageNumber(1);

        try
        {
          $vars['[notifications]'] = $view->widgetHTML($widget, $notifications);
          $remove = array("\n", "\r\n", "\r");
          $vars['[notifications]'] = str_replace($remove, ' ', $vars['[notifications]']);
          $remove = array("    ", "   ", "  ");
          $vars['[notifications]'] = str_replace($remove, " ", $vars['[notifications]']);
        } catch (Exception $e){

        }
      }

      $vars['[profile_url]'] .= $recipient->getHref();
    } else {

      $vars['[unsubscribe_url]'] = $vars['[site_url]'].'/updates/ajax/unsubscribe/'.$recipient->email_address;
      $vars['[displayname]'] = $recipient->name;
      $vars['[email]'] = $recipient->email_address;
    }

    $variables = array('keys'=>array_keys($vars), 'replaces'=>array_values($vars));

    return $variables;
  }

  public function getWidgetsVariables($message)
  {
    /**
     * @var $view Zend_View
     * @var $contentTb Updates_Model_DbTable_Content
     * @var $widgetTb Updates_Model_DbTable_Widgets
     * @var $authTb Authorization_Model_DbTable_Allow
     */
    $view = Zend_Registry::get('Zend_View');
    $contentTb = Engine_Api::_()->getDbtable('content', 'updates');
    $widgetTb = Engine_Api::_()->getDbtable('widgets', 'updates');
    $widget_array = $widgetTb->getWidgets();
    $authTb = Engine_Api::_()->getDbtable('allow', 'authorization')->info('name');
    $widgets = array();

    foreach($widget_array as $wids)
    {
      foreach ($wids as $widget)
      {
        $widgets[$widget['name']] = $widget;
      }
    }

    $standards = array('[site_url]','[profile_url]','[displayname]','[email]','[unsubscribe_url]');
    preg_match_all('/\[+[a-zA-Z0-9\/\-\_\s@\.\$\&\?=!\{\}\:\;\,\%\#\*\(\)\+\'\"]*\]/', $message, $matches);

    $suggestWidgets = array('mixed_recommendation','recommended_members','recommended_pages');
    $widgetsVars = array();
    $matches[0] = str_replace('notifications', "notifications  title='Your Notifications'  count='10'", $matches[0]);

		foreach ($matches[0] as $match)
		{
			try{
				$title = ''; $count=0; $name='';
				if (!in_array($match, $standards))
				{
					$replace_tmp = str_replace(array('[', ']'), '', $match);
					preg_match_all('/title=[\'\"]{1}[a-zA-Z0-9\!\@\#\$\%\^\&\*\(\)\_\-\+\=\{\}\s\t]+/', $replace_tmp, $title_match);

					if(isset($title_match[0][0]))
					{
						$title = str_replace(array("title='", 'title="'), '', $title_match[0][0]);
					}

					preg_match_all('/count=[\'\"]{1}[0-9]+/', $replace_tmp, $count_match);

					if (isset($count_match[0][0]))
					{
						$count = str_replace(array("count='", 'count="'), '', $count_match[0][0]);
					}
					preg_match_all('/select=[\'\"]{1}[0-9]+/', $replace_tmp, $select_match);

					if (isset($select_match[0][0]))
					{
						$select = str_replace(array("select='", 'select="'), '', $select_match[0][0]);
					}

					$name = trim(str_replace(array("title='" . $title . "'", 'title="' . $title . '"', "count='" . $count . "'", 'count="' . $count . '"'), '', $replace_tmp));

          $html = '';

					if (in_array($name, array_keys($widgets)) && !in_array($name, $suggestWidgets))
					{
            $widgets[$name]['title'] = $title;
            $widgets[$name]['count'] = (int)$count;
            $widgets[$name]['authTb'] = $authTb;
            $widgets[$name]['select'] = $select;

            /**
             * @var $items Engine_Db_Table_Rowset
             */
            if(
              null != ($items = $contentTb->getContentData($name, $widgets[$name])) &&
              $items->count() > 0
            ){
              $html = $view->widgetHTML($widgets[$name], $items, array('campaign'=>true));
            }
					}

          $widgetsVars[$match] = $html;
				}
			} catch (Exception $e) {
//				print_log($e->__toString());
			}
		}

    $variables = array('keys'=>array_keys($widgetsVars), 'replaces'=>array_values($widgetsVars));
    return $variables;
  }

  public function getSuggestWidgetsVariables($message, $recipient = null)
  {
    if (!$recipient || !($recipient instanceof User_Model_User)) {
      return false;
    }

    $view = Zend_Registry::get('Zend_View');
    $contentTb = Engine_Api::_()->getDbtable('content', 'updates');
    $widgetTb = Engine_Api::_()->getDbtable('widgets', 'updates');
    $widget_array = $widgetTb->getWidgets();
    $autTb = Engine_Api::_()->getDbtable('allow', 'authorization')->info('name');
    $widgets = array();

    foreach($widget_array as $wids)
    {
      foreach ($wids as $widget)
      {
        $widgets[$widget['name']] = $widget;
      }
    }

    $standards = array('[site_url]','[notifications]','[profile_url]','[displayname]','[email]','[unsubscribe_url]');
    preg_match_all('/\[+[a-zA-Z0-9\/\-\_\s@\.\$\&\?=!\{\}\:\;\,\%\#\*\(\)\+\'\"]*\]/', $message, $matches);

    $suggestWidgets = array('mixed_recommendation','recommended_members','recommended_pages');
    $widgetsVars = array();

		foreach ($matches[0] as $match)
		{
			try{
				$title = ''; $count=0; $name='';
				if (!in_array($match, $standards))
				{
					$replace_tmp = str_replace(array('[', ']'), '', $match);
					preg_match_all('/title=[\'\"]{1}[a-zA-Z0-9\!\@\#\$\%\^\&\*\(\)\_\-\+\=\{\}\s\t]+/', $replace_tmp, $title_match);

					if(isset($title_match[0][0]))
					{
						$title = str_replace(array("title='", 'title="'), '', $title_match[0][0]);
					}

					preg_match_all('/count=[\'\"]{1}[0-9]+/', $replace_tmp, $count_match);

					if (isset($count_match[0][0]))
					{
						$count = str_replace(array("count='", 'count="'), '', $count_match[0][0]);
					}

					$name = trim(str_replace(array("title='" . $title . "'", 'title="' . $title . '"', "count='" . $count . "'", 'count="' . $count . '"'), '', $replace_tmp));
					if (in_array($name, $suggestWidgets))
					{
            $widgets[$name]['title'] = $title;
            $widgets[$name]['count'] = (int)$count;
            $widgets[$name]['authTb'] = $autTb;

            $items = call_user_func(array($contentTb, 'new_' . $name), $recipient, $widgets[$name]);
            if ($items) {
              $html = $view->widgetHTML($widgets[$name], $items, array('campaign'=>true));
            } else {
              $html = '';
            }

            if (strlen($html) > 0)
            {
              $widgetsVars[$match] = $html;
            }

					}
				}
			} catch (Exception $e){
				//Zend_Registry::get('Zend_View')->printArr($match);
				//echo $e;
				//exit();
			}
		}

    $variables = array('keys'=>array_keys($widgetsVars), 'replaces'=>array_values($widgetsVars));
    return $variables;
  }
}