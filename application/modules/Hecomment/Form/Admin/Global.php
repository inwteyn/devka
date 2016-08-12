<?php

/**
 * Created by Hire-Experts LLC.
 * Author: Mirlan
 * Date: 26.05.2015
 * Time: 11:33
 */
class Hecomment_Form_Admin_Global extends Engine_Form
{
    public function init()
    {
        $this->setTitle('HECOMMENT_Global Settings')
            ->setDescription('HECOMMENT_These settings affect all members in your community.');

        $settings = Engine_Api::_()->getApi('settings', 'core');

        $this->addElement('checkbox', 'hecomment_notification_enabled', array(
            'value' => $settings->getSetting('hecomment.notification.enabled', true),
            'description' => 'HECOMMENT_Advanced comments notifications'
        ));

        $this->addElement('checkbox', 'hecomment_advancedcomments_enabled', array(
            'value' => $settings->getSetting('hecomment.advancedcomments.enabled', true),
            'description' => 'HECOMMENT_Replace standard comment widget'
        ));

        $this->addElement('checkbox', 'hecomment_edit_enabled', array(
            'value' => $settings->getSetting('hecomment.edit.enabled', true),
            'description' => 'HECOMMENT_Enable edit comments'
        ));

        $this->addElement('checkbox', 'hecomment_hide_reply_enabled', array(
            'value' => $settings->getSetting('hecomment.hide.reply.enabled', true),
            'description' => 'HECOMMENT_Hide comment replies'
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'HECOMMENT_Save Changes',
            'type' => 'submit',
            'ignore' => 'true'
        ));
    }
}