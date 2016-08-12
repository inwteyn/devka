<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AjaxController.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Updates_AjaxController extends Core_Controller_Action_Standard
{
	
  public function indexAction()
  {
    $XmlRequest = new Zend_Controller_Request_Http();
    if (!$XmlRequest->isXmlHttpRequest())
    {
    	header('location: notfound');
    }
    else
    {
    	$form = new Updates_Form_Widgets_Subscribe();
			$this->view->result = $form->subscription($this->_getParam('task'));
    }
  }
  
  public function imageAction()
  {
    $type = $this->_getParam('type');
		$id = $this->_getParam('id');
    if ($type == 'updates'){
      $table = Engine_Api::_()->getDbtable('updates', 'updates');
      $row = $table->fetchRow($table->select()->where('update_id = ?', $id)->limit(1));
      $row->viewed++;
      $row->save();
    } elseif ($type == 'campaigns'){
      $table = Engine_Api::_()->getDbtable('campaigns', 'updates');
      $row = $table->fetchRow($table->select()->where('campaign_id = ?', $id)->limit(1));
      $row->viewed++;
      $row->save();
    }

		header("Cache-Control: no-cache");
		header("Content-type: image/png");
		
		$img_handle = @ImageCreate(1, 1); 
		$back_color = @ImageColorAllocate($img_handle, 255, 255, 255);
		$transparent_bg = @ImageColorTransparent($img_handle, $back_color);
				
		@ImageColorAllocate($img_handle, 0, 0, 0); 
		@ImagePng($img_handle); 
		exit();
		
		header('location: notfound');
  }
  
  public function referredAction()
  {
    $type = $this->_getParam('type');
    $id = $this->_getParam('id');
    $url = $this->_getParam('url');
    $url = urldecode(str_replace('_enc_', '%', $url));

    if ($type == 'updates')
    {
      $widgetTb = Engine_Api::_()->getDbtable('widgets', 'updates');
      $widgetSl = $widgetTb
        ->select()
        ->setIntegrityCheck(false)
        ->from(array('w'=>$widgetTb->info('name')), 'w.module')
        ->group('module');

      $widgets = $widgetTb->fetchAll($widgetSl);

      foreach ($widgets as $widget)
      {
        if (strpos($url, '/module0'.$widget->module.'/') !== false)
        {
          $module = $widget->module;
          $url = str_replace('/module0'.$widget->module, '', $url);
          break;
        }
      }
    }

    $referred_date = date('Y-m-d', strtotime('1/'.date('m/Y', time())));
    $linksTb = Engine_Api::_()->getDbtable('links', 'updates');
    $linksSl = $linksTb
      ->select()
      ->where('referred_date=?',$referred_date)
      ->where('link=?', $url)
      //->where('id=?', $id)
      ->where('type=?', $type)
      ->limit(1);

    if ( null != ($linkRow = $linksTb->fetchRow($linksSl)))
    {
      $linkRow->referred_count++;
      $linkRow->save();
    }
    else
    {
      $link_id = $linksTb->insert(array(
        'referred_date' => $referred_date,
        'link' => $url,
        'referred_count'=>1,
        'id'=>$id,
        'type'=>$type,
        'module'=>$module,
      ));
    }

    $this->_redirect($url);
  }

	public function unsubscribeAction()
	{
    // Unsubscribe from Mailchimp and SE
    $unsubscribedEmail = $_POST['data']['email'];
    if (isset($unsubscribedEmail) && $unsubscribedEmail != '') {
      $subscribersTbl = Engine_Api::_()->getDbtable('subscribers', 'updates');
      $subscribersTbl->unsubscribeUser($unsubscribedEmail);
    }

    // Unsubscribe from SE
    $email = $this->_getParam('email');
    if (isset($email) && $email != '')
    {
      $emailTb = Engine_Api::_()->getDbtable('subscribers', 'updates');

      if ($emailTb->delete(array('email_address = ?' => $email))) {
        $this->view->success = 1;
      }
      else {
        $userTB = Engine_Api::_()->getDbtable('users', 'user');
        $selectUser = $userTB->select()->where('email = ?', $email)->limit(1);

        if (null != ($user = $userTB->fetchRow($selectUser))) {
          $user->updates_subscribed = 0;
          $user->disableHooks(true);
          if ( $user->save()) {
            $this->view->success = 1;
          }
          $user->disableHooks(false);
        }
      }
    }
	}
}