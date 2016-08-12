<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 15.02.12
 * Time: 11:26
 * To change this template use File | Settings | File Templates.
 */
class Touch_Model_DbTable_Integrations extends Engine_Db_Table
{
  protected $_rowClass = 'Touch_Model_Integration';

  protected $_integrations;

  protected $_integrationsAssoc = array();
  protected $_typesAssoc = array(
    'core',
    'standard',
    'extra',
    'he',
    'wl',
  );

  protected $_enabledIntegrationNames;
  public function selectByStatus($status){
    if(is_numeric($status)){
      if($status == 1);
    } else if(is_string($status)){

    }
  }
  public function getIntegration($name)
  {
    if( null === $this->_integrations ) {
      $this->getIntegrations();
    }

    if( !empty($this->_integrationsAssoc[$name]) ) {
      return $this->_integrationsAssoc[$name];
    }

    return null;
  }
  
  public function getIntegrations($type = null)
  {
    if( null === $this->_integrations ) {
      if($type != null)
        $this->_integrations = $this->select()->where('type = ?', $type)->fetchAll();
      else
        $this->_integrations = $this->fetchAll();
      foreach( $this->_integrations as $integration ) {
        $this->_integrationsAssoc[$integration->name] = $integration;
      }
    }

    return $this->_integrations;
  }

  public function getIntegrationsAssoc()
  {
    if( null === $this->_integrations ) {
      $this->getIntegrations();
    }
    
    return $this->_integrationsAssoc;
  }

  public function hasIntegration($name)
  {
    return !empty($this->_integrationsAssoc[$name]);
  }

  public function isIntegrationEnabled($name)
  {
    return in_array($name, $this->getEnabledIntegrationNames());
  }

  public function getEnabledIntegrationNames()
  {
    if( null === $this->_enabledIntegrationNames ) {
      $this->_enabledIntegrationNames = $this->select()
          ->from($this, 'name')
          ->where('enabled = ?', true)
          ->query()
          ->fetchAll(Zend_Db::FETCH_COLUMN);
    }

    return $this->_enabledIntegrationNames;
  }
  public function getIntegrationCountBy($where, $by){

  }
  public function getTypeAssoc($type_num){
    return $this->_typesAssoc[$type_num-1];
  }
}
