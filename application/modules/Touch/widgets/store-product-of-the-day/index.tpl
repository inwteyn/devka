<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

?>
<div class="store-widget">
  <div class="user_home_photo">
    <?php if($this->photo): ?>
   		<?php echo $this->htmlLink(
   			$this->product->getHref(),
   			$this->itemPhoto($this->product, 'thumb.profile', '', array('class' => 'img-of-the-day'))
   		);
   		?>
  </div>
  <h4><?php echo $this->translate($this->widget_title)?>:</h4>

	<?php else: ?>
  <div class="user_home_photo">
		<?php echo $this->htmlLink(
			$this->product->getHref(),
			'<img class="img-of-the-day" src="application/modules/Store/externals/images/nophoto_product_thumb_profile.png" />'
		);
		?>
  </div>
	<?php endif; ?>
    <b>
      <?php echo $this->htmlLink(
     		$this->product->getHref(),
     		$this->product->getTitle()
     	); ?>
    </b>
    <div class="rating" style="clear: none;">
      <?php echo $this->itemRate($this->product->getType(), $this->product->getIdentity()); ?>
    </div>

  <?php if($this->product->sponsored): ?>
    <img title="<?php echo $this->translate('STORE_Sponsored'); ?>" class="of-the-day" src="application/modules/Store/externals/images/admin/sponsored1.png">
  <?php endif; ?>
  <?php if($this->product->featured): ?>
    <img title="<?php echo $this->translate('STORE_Featured'); ?>" class="of-the-day" src="application/modules/Store/externals/images/admin/featured1.png">
  <?php endif; ?>
	<br />

</div>