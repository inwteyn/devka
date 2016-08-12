<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: List.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Wall_Form_Admin_Changefeed extends Wall_Form_Subform
{
    public function init()
    {
        $this->setTitle('WALL_ADMIN_CHANGE_FEED_TITLE');
        $this->addElement('Checkbox', 'wall_change_feed', array('description' => 'WALL_CHANGE_FEED_SAVE'));
    }

    public function applyDefaults()
    {
        $setting = Engine_Api::_()->getDbTable('settings', 'core');
        $this->getElement('wall_change_feed')->setValue($setting->getSetting('wall.change.feed.save', 0));
    }

    public function saveValues()
    {
        Engine_Api::_()->getDbTable('settings', 'core')->setSetting('wall.change.feed.save', $this->getElement('wall_change_feed')->getValue() ? 1 : 0);

        $enabled =  $this->getElement('wall_change_feed')->getValue() ? 1 : 0;

        Engine_Api::_()->getDbTable('content', 'core')->update(
            array('name' => $enabled ? 'wall.feed' : 'activity.feed'),
            array('name = ?' => $enabled ? 'activity.feed' : 'wall.feed')
        );
    }
}