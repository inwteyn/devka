<?php
/**
 * SocialEngine
 *
 * @category Application_Extensions
 * @package Timeline
 * @copyright Copyright Hire-Experts LLC
 * @license http://www.hire-experts.com
 * @version $Id: Controller.php 2/11/12 3:23 PM mt.uulu $
 * @author Mirlan
 */

/**
 * @category Application_Extensions
 * @package Timeline
 * @copyright Copyright Hire-Experts LLC
 * @license http://www.hire-experts.com
 */


class Timeline_Widget_ContentController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }


    /**
     * @var $subject Timeline_Model_User
     */
    if (Engine_Api::_()->core()->hasSubject()){
      $subject = Engine_Api::_()->core()->getSubject();

      if ($subject instanceof User_Model_User){
        $subject = Engine_Api::_()->getDbTable('users', 'timeline')->findRow($subject->getIdentity());
      }
      if (!$subject->authorization()->isAllowed($viewer, 'view') || !in_array($subject->getType(), Engine_Api::_()->timeline()->getSupportedItems()))
      {
        return $this->setNoRender();
      }
    }

    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    if($subject->getType()=='user'){   $this->view->dates = $dates = Engine_Api::_()->timeline()->timelineDates($subject);}
    if($subject->getType()=='page'){   $this->view->dates = $dates = Engine_Api::_()->timeline()->timelinePageDates($subject);}
    if($subject->getType()=='group'){ $this->view->dates = $dates = Engine_Api::_()->timeline()->timelineGroupDates($subject);}
  }
}
