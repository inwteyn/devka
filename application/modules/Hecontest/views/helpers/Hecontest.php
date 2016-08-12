<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Hecontest.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 04.10.13
 * Time: 14:21
 * To change this template use File | Settings | File Templates.
 */
class Hecontest_View_Helper_Hecontest extends Zend_View_Helper_Abstract
{
    public function Hecontest()
    {
        return $this;
    }

    public function getJoinForm()
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        if ($viewer->getIdentity()) {
            $form = new Hecontest_Form_Join();
            $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'join', 'format' => 'json'), 'hecontest_general', true));
        }
        return $form->render($this->view);
    }
}
