<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 28.08.12
 * Time: 9:58
 * To change this template use File | Settings | File Templates.
 */
class Donation_Model_FinInfo extends Core_Model_Item_Abstract
{


  public function getEmailFin($donation_id){

    $table = Engine_Api::_()->getItemTable('donation_fin_info');

    print_die($table);

    $select = $table->select()
      ->from($table->info('name'),array('pemail'))
      ->where('donation_id = ?', $donation_id)
      ->limit(1);

    return $select->query()->fetchColumn();

  }


}
