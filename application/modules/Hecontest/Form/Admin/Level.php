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


class Hecontest_Form_Admin_Level extends Engine_Form
{
    public function init()
    {
        $settings = Engine_Api::_()->getDbtable('settings', 'core');
        $this
            ->setTitle('Hecontest Level Settings')
            ->setAttrib('class', 'hecontest-admin-form')
            ->setDescription('Hecontest_ADMIN_LEVEL_SETTINGS_DESCRIPTION');

        $levels = array();
        $table = Engine_Api::_()->getDbtable('levels', 'authorization');

        foreach ($table->fetchAll($table->select()) as $row) {
            $levels[$row['level_id']] = $row['title'];
        }

        $this->addElement('Select', 'level_id', array(
            'label' => 'Member Level',
            'multiOptions' => $levels,
        ));

        $this->addElement('Radio', 'view', array(
            'label' => 'Allow View Contest?',
            'description' => 'HECONTEST_Allow view contest',
            'multiOptions' => array(
                0 => 'No, do not allow view contest.',
                1 => 'Yes, allow view contest.'
            ),
            'value' => 1,
        ));

        $this->addElement('Radio', 'vote', array(
            'label' => 'Allow Vote In The Contest?',
            'description' => 'HECONTEST_Allow vote in the contest',
            'multiOptions' => array(
                0 => 'No, do not allow vote in the contest.',
                1 => 'Yes, allow vote in the contest.'
            ),
            'value' => 1,
        ));

        $this->addElement('Radio', 'participate', array(
            'label' => 'Allow Participation?',
            'description' => 'HECONTEST_Allow participation',
            'multiOptions' => array(
                0 => 'No, do not allow participation.',
                1 => 'Yes, allow participation.'
            ),
            'value' => 1,
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

    public function setLevelId($id)
    {
        $this->level_id->setValue($id);
        if($id == 5) {
            $this->removeElement('vote');
            $this->removeElement('participate');
        }
    }
}