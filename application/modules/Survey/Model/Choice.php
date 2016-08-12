<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Choice.php 2010-07-02 19:53 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Survey_Model_Choice extends Core_Model_Item_Abstract
{
  public function getTable()
  {
    if (is_null($this->_table)) {
      $this->_table = Engine_Api::_()->getDbtable('choices', 'survey');
    }

    return $this->_table;
  }
}