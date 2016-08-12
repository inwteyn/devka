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
<script type="text/javascript">
  en4.core.runonce.add(function() {
    var status = $$('.layout_store_product_status');
    status.setStyle('padding-bottom', 30);
  });
</script>
<div class="user_home_photo">
<?php if($this->photo): ?>
	<?php echo $this->htmlLink(
		$this->product->getHref(),
		$this->itemPhoto($this->product, 'thumb.profile', '', array('class' => 'img-of-the-day'))
	);
	?>
</div>
<?php else: ?>
<div class="user_home_photo">
<?php echo $this->htmlLink(
$this->product->getHref(),
'<img class="img-of-the-day" src="application/modules/Store/externals/images/nophoto_product_thumb_profile.png" />'
);
?>
</div>
<?php endif; ?>

<h3>
  <span style="float: left;"><?php echo ( '' != trim($this->product->getTitle()) ? $this->product->getTitle() : '<em>' . $this->translate('Untitled') . '</em>'); ?></span>
  <div class="product-sponsored-featured">
    <span>
      <?php if ($this->product->sponsored) : ?>
        <img class="icon" src="application/modules/Store/externals/images/sponsoredBig.png" title="<?php echo $this->translate('STORE_Sponsored'); ?>">
      <?php endif; ?>
      <?php if ($this->product->featured) : ?>
        <img class="icon" src="application/modules/Store/externals/images/featuredBig.png" title="<?php echo $this->translate('STORE_Featured'); ?>">
      <?php endif; ?>
    </span>
  </div>
</h3>