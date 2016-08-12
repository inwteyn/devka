<?php
/**
 * Created by PhpStorm.
 * User: azama_000
 * Date: 08.09.15
 * Time: 11:04
 */

class Apptouch_CometchatController extends Apptouch_Controller_Action_Bridge{

    public function indexIndexAction(){

        $viewer = Engine_Api::_()->user()->getViewer();
        $user_id = $viewer->getIdentity();
        $rTable = Engine_Api::_()->getDbTable('recipients','messages');
        $rName = $rTable->info('name');
        $select = $rTable->select()
            ->from($rName, array($rName.'.conversation_id'))
            ->where($rName.'.user_id = ?', $user_id)
            ->where($rName.'.inbox_read = ?', 0)
            ->where($rName.'.inbox_deleted = ?', 0);
        $unreadMessages = $rTable->fetchAll($select);
        $paginator = Engine_Api::_()->getItemTable('messages_conversation')
            ->getInboxPaginator($viewer);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $unread = Engine_Api::_()->messages()->getUnreadMessageCount($viewer);
        if($unreadMessages){
            foreach($unreadMessages as $message_id) {
                $this->message_ids[] = $message_id['conversation_id'];
            }
        }

        /*$this
          ->add($this->component()->navigation('messages_main'), -1);
        if (Engine_Api::_()->user()->getViewer()->getIdentity())
          $this->add($this->component()->quickLinks('quick'));*/

        if(!$paginator->getTotalItemCount()) {
            $this
                ->setFormat('browse')
                /*->add($this->component()->date(array(
                    'title' => $this->view->translate(array('You have %1$s new message, %2$s total', 'You have %1$s new messages, %2$s total', $unread),
                      @$this->view->locale()->toNumber($unread),
                      @$this->view->locale()->toNumber($paginator->getTotalItemCount()))
                  )
                )
              )*/
                ->add($this->component()->html($this->view->translate('You have no message!') . '<br/>'))
                ->renderContent();
            return;
        }


        $this
            ->setFormat('browse')
            ->add($this->component()->itemList($paginator, 'inboxItemData', array('listPaginator' => true,)))
//      ->add($this->component()->paginator($paginator))
            ->renderContent();;
    }
    public function inboxItemData(Core_Model_Item_Abstract $item) {
        $fields = array(
            'photo' => $item->getOwner()->getPhotoUrl('thumb.normal')
        );
        if(in_array($item->conversation_id, $this->message_ids)) {
            $fields['attrsLi'] = array(
                'data-theme' => 'e'
            );
        }
        return $fields;
    }
}