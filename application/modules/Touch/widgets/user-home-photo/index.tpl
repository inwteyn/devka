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

<div class="user_home_photo">
  <?php echo $this->htmlLink($this->viewer()->getHref(), $this->itemPhoto($this->viewer(), 'thumb.normal')) ?>
</div>
<h3 style="overflow: hidden; float: left;">
  <?php echo $this->translate('Hi %1$s!', $this->viewer()->getTitle()); ?>
</h3>
