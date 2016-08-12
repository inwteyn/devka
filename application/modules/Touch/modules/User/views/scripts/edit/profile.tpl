<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: profile.tpl 2012-01-27 01:01:32Z ulan $
 * @author     John
 */
?>
<?php if(!$this->posted && count($this->navigation) > 0 ): ?>
<h3 class="edit_profile_headline">
  <?php echo $this->translate('Edit My Profile');?>
</h3>
<?php
		// Render the menu
		echo $this->navigation()
->menu()
->setContainer($this->navigation)
->setPartial(array('navigation/index.tpl', 'touch'))
->render();
?>
<?php endif; ?>
<!---->
<?php
//  /* Include the common user-end field switching javascript */
//  echo $this->partial('_jsSwitch.tpl', 'fields', array(
//      'topLevelId' => (int) @$this->topLevelId,
//      'topLevelValue' => (int) @$this->topLevelValue
//    ))
//?>
<!---->
<div id="navigation_content">
<?php echo $this->form->setAttrib('class', 'global_form touchform')->render($this) ?>
  </div>