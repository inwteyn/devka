<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: RebuildPrivacy.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
//class Hecontest_Plugin_Job_Maintenance_Update extends Core_Plugin_Job_Abstract
class Hecontest_Plugin_Job_Maintenance_Update extends Core_Plugin_Task_Abstract
{
    //protected function _execute()
    public function execute()
    {
        $active = Engine_Api::_()->getItemTable('hecontest')->getActiveContest();
        if(!$active) {
            Engine_Api::_()->getItemTable('hecontest')->autoStartContest();
            return;
        }
        if($active->timeToStop()) {
            Engine_Api::_()->getItemTable('hecontest')->autoStartContest();
            return;
        }
    }
}
