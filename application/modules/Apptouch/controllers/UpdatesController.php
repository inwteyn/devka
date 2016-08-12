<?php


class Apptouch_UpdatesController extends Apptouch_Controller_Action_Bridge
{
	public function settingsInit()
	{
		$this->_helper->requireUser();
	}

  public function settingsIndexAction()
  {
  	if(
  			!$this->_helper->api()->user()->getViewer()->getIdentity()
  			||
  		 	!$this->_helper->requireAuth()->setAuthParams('updates', null, 'use')->isValid()
  		)
  		{
        $this->renderContent();
  			return;
  		}

    $user = Engine_Api::_()->user()->getViewer();
    Engine_Api::_()->core()->setSubject($user);

    $navigation = $this->_helper->api()
      ->getApi('menus', 'apptouch')
      ->getNavigation('user_settings', array());
    $this->add($this->component()->html("<h2>".$this->view->translate('UPDATES_My Settings')."</h2>"));
    $this->add($this->component()->navigation($navigation));

  	$form = new Updates_Form_Subscribe();

    if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();
 			$user->updates_subscribed = $values['subscribe'];

      if ($user->save())
      {
      	$form->addNotice('UPDATES_Changes have been successfully saved.');
        $this->add($this->component()->form($form))->renderContent();
        return;
      }
      else
      {
      	$form->addError('UPDATES_An error has been occurred while subscribing!!!');
        $this->add($this->component()->form($form))->renderContent();
        return;
      }

    }

    $form->populate(array('subscribe'=>$user->updates_subscribed));
        $this->add($this->component()->form($form));
        $this->renderContent();
  }
  public function ajaxReferredAction(){
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
  $this->_helper->redirector->gotoUrl($url);

  }
}