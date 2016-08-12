<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Usernotes
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: save.tpl 2010-07-02 17:53 vadim $
 * @author     Vadim
 */
?>

<?php
	$this->headScript()->appendFile($this->baseUrl() . '/application/modules/Usernotes/externals/scripts/core.js');
?>

<div class="global_form_popup">
  <?php echo $this->create_form->render($this) ?>
</div>
