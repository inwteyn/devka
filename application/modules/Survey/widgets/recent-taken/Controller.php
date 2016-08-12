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

class Survey_Widget_RecentTakenController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $element = $this->getElement();
    $element->setTitle('');

    $table = Engine_Api::_()->getDbtable('surveys', 'survey');
    $select = $table->select();
    $qtName = $table->info('name');

    $rtTable = Engine_Api::_()->getDbtable('takes', 'survey');
    $ptName = $rtTable->info('name');

    $select
      ->setIntegrityCheck(false)
      ->from($qtName)
      ->joinLeft($ptName, "( `$ptName`.`survey_id` = `$qtName`.`survey_id` )", array("took_date" => "$ptName.took_date"))
      ->where("$ptName.took_date IS NOT NULL")
      ->where('published = ?', 1)
      ->where('approved = ?', 1)
      ->group("$qtName.survey_id")
      ->order("took_date DESC")
      ->limit(5);
      
    $this->view->surveyes = $table->fetchAll($select);

    if (!$this->view->surveyes || $this->view->surveyes->count() == 0) {
      return $this->setNoRender();
    }
  }
}