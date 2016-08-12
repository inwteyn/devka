<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: general.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     Steve
 */
?>

<?php if(!$this->posted && count($this->navigation) > 0 ): ?>
<h3 class="settings_headline">
  <?php echo $this->translate('My Settings');?>
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
<div id="navigation_content">
  <div>
<!--    --><?php //if ($this->form->saveSuccessful): ?>
<!--      <h3>--><?php //echo $this->translate('Settings were successfully saved.');?><!--</h3>-->
<!--    --><?php //endif; ?>
    <?php echo $this->form->setAttrib('class', 'global_form touchform')->render($this) ?>
  </div>
</div>

