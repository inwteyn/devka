<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Questions.php 2010-07-02 19:27 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Survey_Model_DbTable_Questions extends Engine_Db_Table
{
  protected $_rowClass = "Survey_Model_Question";
}