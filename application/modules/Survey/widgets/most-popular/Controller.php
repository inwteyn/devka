<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-07-02 19:52 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Survey_Widget_MostPopularController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $element = $this->getElement();
    $element->setTitle('');

    $surveyzesTbl = Engine_Api::_()->getDbtable('surveys', 'survey');
    $select = $surveyzesTbl->select();

    $select
      ->where('published = ?', 1)
      ->where('approved = ?', 1)
      ->order("view_count DESC")
      ->limit(5);
      
    $this->view->surveyes = $surveyzesTbl->fetchAll($select);

    if (!$this->view->surveyes || $this->view->surveyes->count() == 0) {
      return $this->setNoRender();
    }
  }
}