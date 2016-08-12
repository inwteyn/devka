<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: DefaultProviders.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Engine_Form_Decorator_GatewayDescription extends Zend_Form_Decorator_Abstract
{
  protected $_placement = null;
  
  public function render($content)
  {
    $translate = Zend_Registry::get('Zend_Translate')->_('STORE_Show instructions');

    $label = $this->getOption('label');
    $description = $this->getOption('description');
    $id = $this->getOption('id');
    $style = ($id == 2) ? 'opacity: 0;' : '';
    $html = "<div id='gtw-wrapper-{$id}' class='gateway_description' style='{$style}'>
      <div class='gateway_label'>{$label}</div>
      <div class='form-wrapper'>
        <a style='' data-id='{$id}' href='javascript://' onclick='toggleInstructions(this); '>
            {$translate}
        </a>
        <div id='gtw-inst-{$id}' class='store-gateway-instructions'>
            {$description}
        </div>
      </div>
      <ul style='display: none;' id='form-errors' class='form-errors'></ul>
      {$content}
    </div>";

    return $html;
  }
}