<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecontest_Widget_ContestCountdownController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        if (Engine_Api::_()->core()->hasSubject('hecontest')) {
            $contest = Engine_Api::_()->core()->getSubject('hecontest');
        }
        if(!$contest){
            $this->setNoRender();
            return;
        }
        $contestTbl = Engine_Api::_()->getDbTable("hecontests", "hecontest");

        $contest = $contestTbl->getActiveContest($contest->getIdentity());

        if (!$contest) {
            $this->setNoRender();
            return;
        }

        if($contest->timeToStop()) {
            $this->setNoRender();
            return;
        }

        $this->view->contest = $contest;
        $result = $contest->timeToFinish();

        if ($result->invert) {
            $this->setNoRender();
            return;
        }

        $this->view->result = $result;
    }
}
