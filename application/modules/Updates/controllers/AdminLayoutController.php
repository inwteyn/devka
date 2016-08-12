<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminLayoutController.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Updates_AdminLayoutController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    // if demoadmin
    if( defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER ) {
      $this->view->engine_admin_neuter = true;
    }

  	$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('updates_admin_main', array(), 'updates_admin_main_layout');

    $widgetTable = Engine_Api::_()->getDbtable('widgets', 'updates');
    $contentTable = Engine_Api::_()->getDbtable('content', 'updates');

    $this->view->contentWidgets = $contentWidgets = $widgetTable->getWidgets();

	  $contentByName = array();
    foreach( $contentWidgets as $infos ) {
			foreach($infos as $info) {
        $contentByName[$info['name']] = $info;
			}
    }
    $this->view->contentByName = $contentByName;

    $contentRowset = $contentTable->fetchAll($contentTable->select()->order('order ASC'));
    $contentStructure = $contentTable->prepareContentStructure($contentRowset);
    foreach( $contentStructure as &$info1 ) {
			if( !in_array($info1['name'], array('top', 'bottom', 'main')) || $info1['type'] != 'container' ) {
				$error = true;
				break;
			}
			foreach( $info1['elements'] as &$info2 ) {
				if( !in_array($info2['name'], array('left', 'middle', 'right')) || $info1['type'] != 'container' ) {
					$error = true;
					break;
				}
			}
			// Re order second-level elements
			usort($info1['elements'], array($contentTable, '_reorderContentStructure'));
    }

    $modulesTbl = Engine_Api::_()->getDbTable('modules', 'core');
    $this->view->coreItem = $modulesTbl->getModule('core')->toArray();
		$this->view->contentStructure = $contentStructure;
  }

  public function previewAction()
  {
		$this->view->headTranslate(array(
			'UPDATES_All unsaved changes to content will be lost'
    ));

  	$settings = Engine_Api::_()->getApi('settings', 'core');
		$this->view->form = $form =  new Updates_Form_Admin_Layout_Preview();

		if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) )
    {
  		$values = $this->_getAllParams();
			if (isset($values['bgcolor']))
				$settings->__set('updates.background.color', $values['bgcolor']);

			if ( isset($values['fncolor']))
				$settings->__set('updates.font.color', $values['fncolor']);

			if (isset($values['tlcolor']))
				$settings->__set('updates.titles.color', $values['tlcolor']);

			if (isset($values['lkcolor']))
				$settings->__set('updates.links.color', $values['lkcolor']);

			if (isset($values['blacklist']))
      {
				$blacklist = Zend_Json::decode($values['blacklist']);
				$widget_names = "'" . implode("','", array_keys($blacklist)) . "'";

        $table = Engine_Api::_()->getDbtable('widgets', 'updates');
				$select = $table->select()->where("name IN ({$widget_names})");
        $widgets = $table->fetchAll($select);

        if ($values['remove'] == 'true')
        {
          foreach ($widgets as $widget)
          {
            $bl = array();

            foreach($blacklist[$widget->name] as $id){
              if ($id != 0) $bl[] = $id;
            }
            $refreshedBlacklist = array();
            $currentBlacklist = explode(',',$widget->blacklist);
            foreach($currentBlacklist as $blacklistItem) {
              if (!in_array($blacklistItem, $bl)) {
                $refreshedBlacklist[] = $blacklistItem;
              }
            }
            if (empty($refreshedBlacklist)) {
              $widget->blacklist = NULL;
            } else {
              $widget->blacklist = implode(",", $refreshedBlacklist);
            }

            $widget->save();
          }
        }
        else
        {
          foreach ($widgets as $widget)
          {
            $bl = array();

            foreach($blacklist[$widget->name] as $id){
              if ($id != 0)$bl[] = $id;
            }
            if (count($bl) === 0){
              $widget->blacklist = NULL;
            } else {
              $widget->blacklist = implode(",", $bl);
            }

            $widget->save();
          }
        }
			}
		}

    // Set view script
    $this->_helper->layout->setLayout('admin-simple');
    $this->getViewScript('preview');

    /**
     * Prepare updates
     *
     * @var $table Updates_Model_DbTable_Updates
     */
		$table = Engine_Api::_()->getDbtable('updates', 'updates');
    if (null == ($updates = $table->prepareUpdate(array('preview'=>true)))) return;

    $viewer = Engine_Api::_()->user()->getViewer();

    $notificationsHTML = '';
    if ((int) Engine_Api::_()->getDbtable('notifications', 'activity')->hasNotifications($viewer))
    {
      $notifications = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationsPaginator($viewer);
      $notifications->setCurrentPageNumber(1);

      $widget = Engine_Api::_()->getDbtable('widgets', 'updates')->getWidget(array('name'=>'notifications'))->toArray();
      $widget['title'] = $this->view->translate('UPDATES_'.$widget['params']['title']);

      $notificationsHTML = $this->view->widgetHTML($widget, $notifications);
      $notificationsHTML = str_replace(
                  array('href="', "href='", 'src="/', "src='/", '<br>', '<br/>'),
                  array(
                       'target="_blank" href="http://'.$_SERVER['HTTP_HOST'].$this->view->baseUrl(),
                       "target='_blank' href='http://".$_SERVER['HTTP_HOST'].$this->view->baseUrl(),
                       'src="http://' . $_SERVER['HTTP_HOST'] . $this->view->baseUrl().'/',
                       "src='http://" . $_SERVER['HTTP_HOST'] . $this->view->baseUrl().'/',
                       " ", " "
                  ),
                  $notificationsHTML);
    }

    $mail = Engine_Api::_()->getApi('settings', 'core')->core_mail;

    $this->view->message = $updates->message = str_replace(array('[displayname]', '[email]', '[notifications]', '[mixed_recommendation]', '[recommended_members]', '[recommended_pages]'),
                           array($mail['name'], $mail['from'], $notificationsHTML),
                           $updates->message
                           );
  }

  public function widgetAction()
  {
  	$content_id = $this->_getParam('content_id', 0);
		$name = $this->_getParam('name');

		if( null === $content_id  && null === $name) {
      throw new Exception('no content found with id: ' . $content_id . ' or widget with name '.$name);
    }

    /**
     * @var $contentTb Updates_Model_DbTable_Content
     */
    $contentTb = Engine_Api::_()->getDbtable('content', 'updates');
		$content = $contentTb->getContent($content_id);
		if(!isset($content) && count($content) == 0 ){
			$content = Engine_Api::_()->getDbtable('widgets', 'updates')->getWidget(array('name'=>$name));
		}

		if (count($content) == 1)
		{

			$this->view->form = $form = new Updates_Form_Admin_Layout_Widget(array('params'=>$content));

			$form->setTitle($content->title)
					 ->setDescription($content->description);
			if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
        
        $values = $form->getValues();


        $values = $form->getValues();

        if(@$_POST['select']>0){
          $form->select->setValue($_POST['select']);
          $values['select'] = $_POST['select'];
        }

        if (array_key_exists('html', $values)){
          $values['html'] = str_replace('../../../undefined', 'http://' . $_SERVER['HTTP_HOST'], $values['html']);
        }

        $this->view->values = $values;

				unset($this->view->values['content_id']);
				unset($this->view->values['']);
				$this->view->form = null;
			}

		} else {
			throw new Exception('no content found with id: ' . $content_id);
		}
  }

	public function testemailAction()
  {
		$this->view->form = $form = new Updates_Form_Admin_Layout_Testemail();

		if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()))
		{
      /**
       * @var $table Updates_Model_DbTable_Updates
       * @var $mail Core_Api_Mail
       */
			$test_email = $this->_getParam('test_email');
			$table = Engine_Api::_()->getDbtable('updates', 'updates');
      $mail = Engine_Api::_()->getApi('mail', 'core');

			if (null !== ($updates = $table->prepareUpdate(array('testemail'=>true))))
    	{
				$remove = array("[displayname]", "[email]", '[notifications]');
				$replace = array($test_email, $test_email, '', '');
				$updates->message = str_replace($remove, $replace, $updates->message);

      	$remove = array("\n", "\r\n", "\r");
				$updates->message = str_replace($remove, ' ', $updates->message);
				$remove = array("    ", "   ", "  ");
				$params['updates'] = $message = str_replace($remove, ' ', $updates->message);

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $mailService = $settings->__get('updates.mailservice');

        $messageBody = $params['updates'];

        if ($mailService == 'socialengine')
        {
          if ( $mail->sendSystemRaw($test_email, 'updates', $params) instanceof Core_Api_Mail)
          {
            $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => TRUE,
              'parentRefresh' => FALSE,
              'format'=> 'smoothbox',
              'messages' => array($this->view->translate('UPDATES_Test mail successfully has been sent to ').$test_email),
            ));
          }
        }
        elseif ($mailService == 'sendgrid')
        {
          include_once 'application/modules/Updates/Api/SendGrid_loader.php';

          // Login credentials
          $username = $settings->__get('updates.sendgrid.username');
          $password = $settings->__get('updates.sendgrid.password');

          // Get admin info
          $fromAddress = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.from', 'admin@' . $_SERVER['HTTP_HOST']);
          $fromName = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.name', 'Site Admin');

          $sendgrid = new SendGrid($username, $password);
          $sendgridMail = new SendGrid\Mail();

          $sendgridMail->
            addTo($test_email)->
            setFrom($fromAddress)->
            setFromName($fromName)->
            setSubject('Test Updates')->
            setText(strip_tags($messageBody))->
            setHtml($messageBody);

          $result = $sendgrid->
            web->
            send($sendgridMail);

          // send message
          if ($result) {
            $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => TRUE,
              'parentRefresh' => FALSE,
              'format'=> 'smoothbox',
              'messages' => array($this->view->translate('UPDATES_Test mail successfully has been sent to ').$test_email),
            ));
          }
          else {
            echo "Something went wrong - " . $result;
            exit;
          }
          unset($sendgridMail);
        }
        elseif ($mailService == 'mailchimp')
        {
          if (!class_exists('MCAPI')) {
            include_once 'application/modules/Updates/Api/MCAPI.class.php';
          }
          $apikey = $settings->__get('updates.mailchimp.apikey');
          $api = new MCAPI($apikey);
          $type = 'regular';

          $list_id = $settings->__get('updates.mailchimp.listid');
          $opts['list_id'] = $list_id;
          $opts['subject'] = $settings->__get('updates.mailchimp.subject');
          $opts['from_email'] = $settings->__get('updates.mailchimp.fromemail');
          $opts['from_name'] = $settings->__get('updates.mailchimp.fromname');
          $opts['title'] = $settings->__get('updates.mailchimp.title');
          $opts['tracking'] = array('opens' => true, 'html_clicks' => true, 'text_clicks' => false);
          $opts['authenticate'] = true;

          $messageBody = str_replace('Unsubscribe</a>&nbsp;|', '</a>', $messageBody);

          $content = array('html'=>$messageBody,
                'text' => 'text text text *|UNSUB|*'
          );

          $campaign_id = $api->campaignCreate($type, $opts, $content);

          if ($api->errorCode) {
            echo "Unable to Create New Updates!\n";
            echo "\tCode=".$api->errorCode."\n";
            echo "\tMsg=".$api->errorMessage."\n";
            exit;
          }

          $emails = array($test_email);
          $api->campaignSendTest($campaign_id, $emails);

          if ($api->errorCode) {
            echo "Unable to Send Updates!\n";
            echo "\tCode=".$api->errorCode."\n";
            echo "\tMsg=".$api->errorMessage."\n";
            exit;
          }
          else {
            $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => TRUE,
              'parentRefresh' => FALSE,
              'format'=> 'smoothbox',
              'messages' => array($this->view->translate('UPDATES_Test mail successfully has been sent to ').$test_email),
            ));
          }
          unset($campaign_id);
        }
      }
		}
	}

	public function updateAction()
  {
    /**
     * @var $widgetTable Updates_Model_DbTable_Widgets
     * @var $contentTable Updates_Model_DbTable_Content
     */
		$widgetTable = Engine_Api::_()->getDbtable('widgets', 'updates');
    $contentTable = Engine_Api::_()->getDbtable('content', 'updates');
    $db = $contentTable->getAdapter();
    $db->beginTransaction();

    try {
      $contentRowset = $contentTable->fetchAll($contentTable->select());

      $strucure = $this->_getParam('structure');

			$orderIndex = 1;
      $newRowsByTmpId = array();
      $existingRowsByContentId = array();
			foreach( $strucure as $element ) {
				$id = @$element['id'];
        $tmp_id = @$element['tmp_id'];
        $parent_id = @$element['parent_id'];
        $tmp_parent_id = @$element['parent_tmp_id'];
        $newOrder = $orderIndex++;

				if( empty($id) && empty($tmp_id) ) {
					throw new Exception('content id and tmp content id both empty');
				}

				$contentRow = null;
				if( !empty($id) ) {
					$contentRow = $contentRowset->getRowMatching('id', $id);
					if( null === $contentRow ) {
            throw new Exception('content row missing');
          }
        }

        // Get existing parent row (if any)
        $parentContentRow = null;
        if( !empty($parent_id) ) {
          $parentContentRow = $contentRowset->getRowMatching('id', $parent_id);
        } else if( !empty($tmp_parent_id) ) {
          $parentContentRow = @$newRowsByTmpId[$tmp_parent_id];
        }

        // Existing row
        if( !empty($contentRow) && is_object($contentRow) ) {
          $existingRowsByContentId[$id] = $contentRow;

          // Update row
          if( !empty($parentContentRow) ) {
            $contentRow->parent_id = $parentContentRow->id;
          }

					//Set element params
					if( isset($element['params']) && is_array($element['params'])){
						$contentRow->params = $element['params'];
					}

					//Set container type
          if( $contentRow->type == 'container' ) {
            $newOrder = array_search($contentRow->name, array('top', 'main', 'bottom', 'left', 'right', 'middle')) + 1;
          }

          $contentRow->order = $newOrder;
          $contentRow->save();
        }

        // New row
        else
        {
          if( empty($element['type']) || empty($element['name']) ) {
            throw new Exception('missing name and/or type info');
          }

					$widgetRow = $widgetTable->getWidget(array('name'=>$element['name']));

          if( $element['type'] == 'container' ) {
            $newOrder = array_search($element['name'], array('top', 'main', 'bottom', 'left', 'right', 'middle')) + 1;
          }

          $contentRow = $contentTable->createRow();
          $contentRow->order = $newOrder;
          $contentRow->type = $element['type'];
          $contentRow->name = $element['name'];
					$contentRow->widget_id = $widgetRow->id;
          // Set parent content
          if( !empty($parentContentRow) ) {
            $contentRow->parent_id = $parentContentRow->id;
          }

					//Set element params
					if( isset($element['params']) && is_array($element['params'])){
						$contentRow->params = $element['params'];
					} else {
						$contentRow->params = $widgetRow->params;
					}

          $contentRow->save();

          $newRowsByTmpId[$tmp_id] = $contentRow;
        }
      }

      // Delete rows that were not present in data sent back
      $deletedRowIds = array();
      foreach( $contentRowset as $contentRow ) {
        if( empty($existingRowsByContentId[$contentRow->id])) {
          $deletedRowIds[] = $contentRow->id;
          $contentRow->delete();
        }
      }
      $this->view->deleted = $deletedRowIds;

      // Send back new content info
      $newData = array();
      foreach( $newRowsByTmpId as $tmp_id => $newRow ) {
        $newData[$tmp_id] = $newRow->toArray();
      }

      $this->view->newIds = $newData;

      $this->view->status = true;
      $this->view->error = false;

      $db->commit();

    } catch( Exception $e ) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = true;
    }
  }
}