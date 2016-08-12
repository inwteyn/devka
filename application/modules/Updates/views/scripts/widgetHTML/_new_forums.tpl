<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: module.tpl 2010-07-02 18:53 mirlan $
 * @author     Mirlan
 */
?>
	<?php if ($this->step == 'thumb'): ?>

  <a href="<?php echo $this->item->getHref(); ?>" style="border:1px solid #DDDDDD;display:inline-block;padding:4px;vertical-align:bottom;text-decoration:none;width:48px;height:48px;overflow:hidden">
    <?php echo $this->itemPhoto($this->item, 'thumb.icon'); ?>
  </a>

	<?php elseif($this->step == 'details'): ?>

		<a href="<?php echo $this->item->getHref() ?>" style="font-weight: bold; color:<?php echo $this->linkColor?>; font-size: 12px;text-decoration: none">
			<?php echo $this->item->getTitle(); ?>
		</a>

		<div style="font-size: 10px">
			<?php echo $this->translate('UPDATES_Posted on ') . ' ' . date('d M  Y', strtotime($this->item->creation_date)); ?><br/>

			<?php echo $this->translate('Category'); ?>:
			<a href="<?php echo $this->url(array(), 'forum_general'); ?>" style="color:<?php echo $this->linkColor;?>; text-decoration: none"  class="msgLink">
				<?php echo $this->item->getOwner()->getTitle(); ?>
			</a>
		</div>

	<?php elseif($this->step == 'more_link'): ?>
    <div align="right">
      <a href="http://<?php echo $this->url(array('module'=>'forum'), 'default', true); ?>" style="text-decoration:underline;color:<?php echo $this->linkColor; ?>">
        <?php echo $this->translate('UPDATES_More forums...'); ?>
      </a>
    </div>
	<?php endif; ?>	