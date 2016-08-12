<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: RebuildUser.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Offers_Plugin_Job_Maintenance_RebuildUser extends Core_Plugin_Job_Abstract
{
  protected function _execute()
  {
    $table = Engine_Api::_()->getItemTable('user');

    $position   = $this->getParam('position', 0);
    $progress   = $this->getParam('progress', 0);
    $total      = $this->getParam('total');
    $limit      = $this->getParam('limit', 100);
    $isComplete = false;
    $break      = false;

    if( null === $total ) {
      $total = $table->select()
        ->from($table->info('name'), new Zend_Db_Expr('COUNT(*)'))
        ->query()
        ->fetchColumn(0)
        ;
      $this->setParam('total', $total);
      if( !$progress ) {
        $this->setParam('progress', 0);
      }
      if( !$position ) {
        $this->setParam('position', 0);
      }
    }

    if( $total <= 0 ) {
      $this->_setWasIdle();
      $this->_setIsComplete(true);
      return;
    }

    $count = 0;
    $primaryCol = array_shift($table->info('primary'));

    while( !$break && $count <= $limit ) {

      $item = $table->fetchRow($table->select()
          ->where('`' . $primaryCol . '` >= ?', (int) $position + 1)->order($primaryCol . ' ASC')->limit(1));

      // Nothing left
      if( !$item ) {
        $break = true;
        $isComplete = true;
      }

      // Main
      else {
        $position = $item->getIdentity();
        $count++;
        $progress++;


        foreach (Engine_Api::_()->offers()->getRequireList() as $key => $require){
          $plugin = Engine_Api::_()->offers()->getRequireClass($key);
          if (empty($plugin)){
            continue ;
          }
          if ($plugin){
            $plugin->check($item);
          }

        }

        //Engine_Api::_()->getDbTable('creditbadges', 'offers')->checkOwnerRank($item);

        // Cleanup
        unset($item);
        unset($itemOwner);
        unset($action);
        unset($options);
        unset($maxAllowed);
        unset($maxAllowedIndex);
        unset($i);
        unset($role);
        unset($roleString);
      }

    }


    // Cleanup
    $this->setParam('position', $position);
    $this->setParam('progress', $progress);
    $this->_setIsComplete($isComplete);
    if( $count <= 0 ) {
      $this->_setWasIdle();
    }
  }
}
