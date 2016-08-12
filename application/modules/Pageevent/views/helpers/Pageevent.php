<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Heevent.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Heevent
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
class Pageevent_View_Helper_Pageevent extends Zend_View_Helper_Abstract
{
  public $subject = null;
  public function pageevent()
  {
    return $this;
  }


  public function getComposerForm(){
    $viewer = Engine_Api::_()->user()->getViewer();

    if($viewer->getIdentity()){
      $this->subject = Engine_Api::_()->core()->getSubject();
      $form = new Pageevent_Form_Form($this->subject);
    }
    return $form->render($this->view );
  }


}
