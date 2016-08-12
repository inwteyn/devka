<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Api.php 21.09.12 12:03 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_Model_Api extends Core_Model_Item_Abstract
{
  protected $_owner_type = 'page';

  protected $_modifiedTriggers = false;

  protected $_searchTriggers = false;

  /**
   * @var Engine_Payment_Plugin_Abstract
   */
  protected $_plugin;

  /**
   * @return Engine_Payment_Plugin_Abstract
   */
  public function getPlugin()
  {
    if( null === $this->_plugin ) {

			/**
			* @var $gatewayTb Payment_Model_Gateway
			*/
			if (null == ($gateway = Engine_Api::_()->getItem('payment_gateway', $this->gateway_id))){
				return null;
			}

			Engine_Loader::loadClass($gateway->plugin);
			if ( !class_exists($gateway->plugin) ) {
				return null;
			}

      $class = str_replace('Payment', 'Offers', $gateway->plugin);

      Engine_Loader::loadClass($class);
			if (!class_exists($class)) {
				return null;
			}

      $plugin = new $class($this);

      if( !($plugin instanceof Engine_Payment_Plugin_Abstract) ) {
        throw new Engine_Exception(sprintf('Payment plugin "%1$s" must ' .
            'implement Experts_Payment_Plugin_Abstract', $class));
      }
      $this->_plugin = $plugin;
    }

    return $this->_plugin;
  }

  /**
   * Get the payment gateway
   *
   * @return Experts_Payment_Gateway
   */
  public function getGateway()
  {
    return $this->getPlugin()->getGateway();
  }

  /**
   * Get the payment service api
   *
   * @return Zend_Service_Abstract
   */
  public function getService()
  {
    return $this->getPlugin()->getService();
  }

  public function getTitle()
  {
    /**
     * @var $table Store_Model_DbTable_Gateways
     */
    $table = Engine_Api::_()->getDbTable('gateways', 'payment');

    return $table->select()
      ->from($table, new Zend_Db_Expr('title'))
      ->where('gateway_id = ?', $this->gateway_id)
      ->query()
      ->fetchColumn()
    ;
  }
}