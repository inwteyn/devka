<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: notfound.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>

<div class="layout_content">
	<h2><?php echo $this->translate("Page Is Not Available", array($this->moduleName)) ?></h2>
	<p>
    <?php echo $this->translate("Sorry, this content is not currently available in this version of Touch-Mobile or requested page doesn't exist. You can view this content on") ?>
    <a href="/touch-mode-switch?return_url=/members/home"><?php echo $this->translate("standard mode"); ?></a>
    <?php echo $this->translate("or ") ?>
    <a href="javascript:void(0);" onClick='history.go(-2);'>
      <img src='application/modules/Core/externals/images/back.png' border="0" height="12px" style="vertical-align:middle;">
      <?php echo $this->translate('go to back'); ?>
    </a>
	</p>
</div>
