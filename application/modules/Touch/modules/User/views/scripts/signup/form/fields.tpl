<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: fields.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>

<?php
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'fields', array(
    'topLevelId' => $this->form->getTopLevelId(),
    'topLevelValue' => $this->form->getTopLevelValue(),
  ));
?>

<?php echo Engine_Api::_()->touch()->customForm($this->form)->setAttrib('class', 'global_form')->render($this) ?>
