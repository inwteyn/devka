<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Choices.php 2010-07-02 19:27 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Survey_Model_DbTable_Choices extends Engine_Db_Table
{
  protected $_rowClass = "Survey_Model_Choice";

  public function deleteUserChoices($survey_id, $user_id)
  {
    $select = $this->select()
      ->where('survey_id = ?', $survey_id)
      ->where('user_id = ?', $user_id);

    $choices = $this->fetchAll($select);

    foreach ($choices as $choice) {
      $choice->delete();
    }
  }
}