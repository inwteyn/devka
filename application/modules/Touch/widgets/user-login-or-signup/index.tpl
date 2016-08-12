<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>

<h3>
    <?php echo $this->translate('Sign In or %1$sJoin%2$s', '<a href="'.$this->url(array(), "user_signup").'" class="touchajax">', '</a>'); ?>
</h3>

<div class='layout_content'>
	<?php echo $this->form->setAttrib('class', 'global_form_box')->render($this) ?>
</div>
