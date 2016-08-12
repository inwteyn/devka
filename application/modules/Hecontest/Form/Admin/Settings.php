<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Settings.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Hecontest_Form_Admin_Settings extends Engine_Form
{
    public function init()
    {
        $settings = Engine_Api::_()->getDbtable('settings', 'core');
        $this
            ->setTitle('Settings')
            ->setAttrib('class', 'hecontest-admin-form')
            //->setAttrib('class', 'hecontest-admin-form')
            ->setDescription('Hecontest_ADMIN_SETTINGS_DESCRIPTION');

        $this->addElement('Radio', 'hecontest_settings_autoapprove', array(
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No',
            ),
            'label' => 'Automatically approve posted photos',
            'value' => $settings->getSetting('hecontest.settings.autoapprove', 1)
        ));


        // Element: execute
        $this->addElement('Button', 'execute', array(
            'label' => 'Save Settings',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));
    }
}