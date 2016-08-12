<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-07-02 19:52 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Survey_Plugin_Core
{
  public function onStatistics($event)
  {
    $table = Engine_Api::_()->getDbTable('surveys', 'survey');
    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($table->info('name'), array('COUNT(*) AS count'))
      ->where('published = 1');
    
    $count = $table->getAdapter()->fetchOne($select);
    $event->addResponse($count, 'survey');
  }

  public function onUserDeleteBefore($survey)
  {
    $payload = $survey->getPayload();

    if ($payload instanceof User_Model_User) {
      // Delete surveyzes
      $surveyTable = Engine_Api::_()->getDbtable('surveys', 'survey');
      $surveySelect = $surveyTable->select()->where('user_id = ?', $payload->getIdentity());

      foreach ($surveyTable->fetchAll($surveySelect) as $survey) {
        $survey->delete();
      }

      // Delete results
      $takeTable = Engine_Api::_()->getDbtable('takes', 'survey');
      $takeSelect = $takeTable->select()->where('user_id = ?', $payload->getIdentity());

      foreach ($takeTable->fetchAll($takeSelect) as $took) {
        $took->delete();
      }      
    }
  }
}