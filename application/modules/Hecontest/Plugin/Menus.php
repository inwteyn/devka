<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Menus.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecontest_Plugin_Menus
{

    public function onMenuInitialize_HecontestMainIndex($row)
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->authorization()->isAllowed('hecontest', $viewer, 'view')) {
            return false;
        }
        return false;
    }

    public function onMenuInitialize_HecontestMainJoin($row)
    {
        print_die($row);
        $viewer = Engine_Api::_()->user()->getViewer();
        $contestsTbl = Engine_Api::_()->getDbTable('hecontests', 'hecontest');
        $contest = $contestsTbl->getActiveContest();

        if ($contest) {
            if (!$contest->isParticipant($viewer->getIdentity())) {
                if ($contest->allowJoin($viewer)) {
                    return true;
                }
            }
        }
        return false;
    }
    public function onMenuInitialize_HecontestMainCreate($row)
    {
        if( !Engine_Api::_()->authorization()->isAllowed('hecontest', null, 'create') ) {
            return false;
        }
        return true;
    }
}
