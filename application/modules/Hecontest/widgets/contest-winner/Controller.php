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

class Hecontest_Widget_ContestWinnerController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $contestTbl = Engine_Api::_()->getDbTable("hecontests", "hecontest");

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();

        $module     = $params['module'];
        $controller = $params['controller'];
        $action     = $params['action'];

        if($module == 'hecontest' && $controller == 'index' && $action == 'index') {
            $this->view->lang = 'HECONTEST_Leader';
            $this->view->text = 'HECONTEST_Leader text';
            $contest = $contestTbl->getActiveContest();
        } else {
            $this->view->lang = 'HECONTEST_Winner';
            $this->view->text = 'HECONTEST_Wiiner text';
            $contest = $contestTbl->getRecentContest();
        }

        if (!$contest || !$contest->allowView()) {
            $this->setNoRender();
            return;
        }

        $this->view->contest = $contest;
        $this->view->winner = $winner = $contest->getWinner();

        if(!$winner) {
            $this->setNoRender();
            return;
        }

        $this->view->user = $winner->getUser();

        if(!$winner->votes) {
            $this->setNoRender();
            return;
        }
    }
}
