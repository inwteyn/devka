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

class Hecontest_Widget_ContestRandomPhotosController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        if (Engine_Api::_()->core()->hasSubject('hecontest')) {
            $contest = Engine_Api::_()->core()->getSubject('hecontest');
        } else {
            $contestTbl = Engine_Api::_()->getDbTable("hecontests", "hecontest");
            $contest = $contestTbl->getActiveContest();
        }


        if (!$contest || !$contest->allowView()) {
            $this->setNoRender();
            return;
        }

        $this->view->contest = $contest;
        $this->view->participants = $participants = $contest->getParticipants(
            array(
                'status' => 'approved',
                'order' => 'RAND()',
                'limit' => 3
            )
        );
        if (!$participants->getTotalItemCount()) {
            $this->setNoRender();
            return;
        }
    }
}
