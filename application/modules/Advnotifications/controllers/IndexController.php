<?php

class Advnotifications_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    /**
     * @var $table Activity_Model_DbTable_Notifications
     * @var $item Activity_Model_Notification
     */
    /*$viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbTable('notifications', 'activity');
    $select = $table->select()
      //->where('notification_id=3073')
      ->where('user_id = ?', $viewer->getIdentity())
      ->where('`read` = 0')
      ->where('is_shown = 0')
      ->order('date desc')
      ->limit(1);
    $item = $table->fetchRow($select);

    if (!$item) {
      return;
    }
    $this->view->html = $this->getNotification($item);*/
  }

  public function updateAction()
  {
    /**
     * @var $table Activity_Model_DbTable_Notifications
     * @var $item Activity_Model_Notification
     * @var $object Activity_Model_Action
     */
    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbTable('notifications', 'activity');
    $select = $table->select()
      ->where('user_id = ?', $viewer->getIdentity())
      ->where('`read` = 0')
      ->where('is_shown = 0')
      ->order('date desc')
      ->limit(1);

    $item = $table->fetchRow($select);

    if (!$item) {
      $this->view->status = true;
      return;
    }

    $item->is_shown = 1;
    $item->save();

    $this->getNotification($item);
  }

  private function getNotification($item = null)
  {
    /**
     * @var $item Activity_Model_Notification
     * @var $object Activity_Model_Action
     */
    if (!$item) {
      return '';
    }

    $object = $item->getObject();

    if ($object->getType() == 'activity_action') {
      $content = $object->body;
    } else {
      $content = false;
    }

    $this->view->action_id = $item->getIdentity();
    $this->view->href = $object->getHref();
    $this->view->status = true;

    $nContent = $item->getContent();

    if ($content) {
      $nContent = substr($nContent, 0, strlen($nContent) - 1) . ' :"' . $content . '"';
    }

    $nContent = str_replace(array('<a', '</a'), array('<span', '</span'), $nContent);
    $nContent = $item->getContent();
    $time = $this->view->timestamp(strtotime($item->date));





    switch ($item->type) {
      case 'liked':
        $icon = 'thumbs-up';
        break;
      case 'commented':
        $icon = 'comment';
        break;
      default:
        $icon = false;
    }

    $isHeAdvMessages = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('headvmessages');

    if ($isHeAdvMessages && $item->type == 'message_new' && $item->object_type == 'messages_conversation' &&
      $item->subject_type == 'user'
    ) {
      $this->view->id = $item->object_id;
      $this->view->advMessages = true;
    }

      if($item->type!='friend_request'){

        $this->view->html = $html = $this->view->partial('_notification.tpl', array(
          'user' => $item->getSubject(),
          'time' => $time,
          'icon' => $icon,
          'nContent' => $nContent
        ));

      }else{

          $nContentlink="<div class='rowsemembers_results_links' >

    <span  class='btn_heuser_list btn-headdfriend headvuser_button wp_init link_ok'>
    <i class='hei hei-check hei-3x' data-id='".$item->toArray()['subject_id']."' data-notif='".$item->toArray()['notification_id']."' id='link_ok'></i>
    </span>

    <span  class='btn_heuser_list btn-headdfriend headvuser_button wp_init link_not_ok'>
    <i class='hei hei-times hei-3x' data-id='".$item->toArray()['subject_id']."' data-notif='".$item->toArray()['notification_id']."' id='link_not_ok'></i>
    </span>

</div>";


          $this->view->html = $html = $this->view->partial('_notification.tpl', array(
              'user' => $item->getSubject(),
              'time' => $time,
              'icon' => $icon,
              'nContent' => $nContent,
              'nContentlink'=>$nContentlink
          ));
      }

    return $html;
  }
}
