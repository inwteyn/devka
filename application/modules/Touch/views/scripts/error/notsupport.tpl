<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: notsupport.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>

<div class="layout_content">
	<h2><?php echo $this->translate('TOUCH_Not Support') ?></h2>
	<p>
		<?php echo $this->translate("TOUCH_Touch plugin does not support %s plugin.", array($this->moduleName)) ?>
	</p>

	<br />
	<a href="javascript:void(0);" onClick='history.go(-2);'>
		<img src='application/modules/Core/externals/images/back.png' border="0" height="12px" style="vertical-align:middle;">
		<?php echo $this->translate('Go to back'); ?>
	</a>
</div>