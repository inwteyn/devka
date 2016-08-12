<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Setting.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Hebadge_Form_Admin_Setting extends Engine_Form
{

  public function init()
  {

    $this->setDescription('HEBADGE_FORM_ADMIN_SETTING_DESCRIPTION');

    $this->addElement('radio', 'showuserbadge', array(
      'label' => 'HEBADGE_FORM_ADMIN_SETTING_SHOWUSERBADGE_LABEL',
      'description' => '',
      'multiOptions' => array(
        '1' => 'HEBADGE_FORM_ADMIN_SETTING_SHOWUSERBADGE_LABEL_YES',
        '0' => 'HEBADGE_FORM_ADMIN_SETTING_SHOWUSERBADGE_LABEL_NO'
      ),
      'value' => Engine_Api::_()->getDbTable('settings', 'core')->getSetting('hebadge.showuserbadge', 1)
    ));


    $this->addElement('radio', 'user_approved', array(
      'label' => 'HEBADGE_FORM_ADMIN_SETTING_USER_LABEL_APPROVED',
      'description' => '',
      'multiOptions' => array(
        '' => 'HEBADGE_FORM_ADMIN_SETTING_USER_NOT_APPROVED',
        '1' => 'HEBADGE_FORM_ADMIN_SETTING_USER_APPROVED'
      ),
      'value' => Engine_Api::_()->getDbTable('settings', 'core')->getSetting('hebadge.user_approved', 0)
    ));

    $this->addElement('radio', 'allow_public_view', array(
      'label' => 'HEBADGE_FORM_ADMIN_SETTING_ALLOWPUBLICMEMBERS_LABEL',
      'description' => '',
      'multiOptions' => array(
        '0' => 'HEBADGE_FORM_ADMIN_SETTING_ALLOWPUBLICMEMBERS_LABEL_NO',
        '1' => 'HEBADGE_FORM_ADMIN_SETTING_ALLOWPUBLICMEMBERS_LABEL_YES'
      ),
      'value' => Engine_Api::_()->getDbTable('settings', 'core')->getSetting('hebadge.allow_public_view', 1)
    ));


    $this->addElement('button', 'submit', array(
      'type' => 'submit',
      'label' => 'Save Changes'
    ));

  }

}