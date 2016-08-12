<?php

/**
 * Created by Hire-Experts LLC.
 * Author: Mirlan
 * Date: 26.05.2015
 * Time: 10:00
 */
class Hecomment_AdminManageController extends Core_Controller_Action_Admin
{
    public function indexAction()
    {
        $this->view->form = $form = new Hecomment_Form_Admin_Global();

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        
        $settings = Engine_Api::_()->getApi('settings', 'core');
        
        $settings->setSetting('hecomment.notification.enabled', $values['hecomment_notification_enabled']);
        $settings->setSetting('hecomment.advancedcomments.enabled', $values['hecomment_advancedcomments_enabled']);
        $settings->setSetting('hecomment.edit.enabled', $values['hecomment_edit_enabled']);
        $settings->setSetting('hecomment.hide.reply.enabled', $values['hecomment_hide_reply_enabled']);

        $this->replacesWidgetName($values['hecomment_advancedcomments_enabled'] ? true : false);
        
        $form->addNotice('HECOMMENT_Your changes have been saved.');
    }

    private function replacesWidgetName($enabled)
    {
        Engine_Api::_()->getDbTable('content', 'core')->update(
            array('name' => $enabled ? 'hecomment.comments' : 'core.comments'),
            array('name = ?' => $enabled ? 'core.comments' : 'hecomment.comments')
        );
    }
}
 