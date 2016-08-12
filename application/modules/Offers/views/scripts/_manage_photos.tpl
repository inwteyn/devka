<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manage_photos.php 2012-07-03 11:40 alexander $
 * @author     Alexander
 */
?>
<h3>
  <?php echo $this->htmlLink("javascript:Offers.view({$this->offer->getIdentity()})", $this->offer->getTitle()) ?> (<?php echo $this->translate(array('%s photo', '%s photos', $this->offer->count()),$this->locale()->toNumber($this->offer->count())) ?>)
</h3>

<?php if ($this->paginator->getTotalItemCount() > 0): ?>

<?php if( $this->paginator->count() > 0 ): ?>
  <br />
  <?php echo $this->paginationControl($this->paginator); ?>
<?php endif; ?>
<form id="offers-photo-manage" action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>">
  <?php echo $this->form->offer_id; ?>
  <ul class='offers_editphotos'>
    <?php foreach( $this->paginator as $photo ): ?>
      <li>
        <div class="offers_editphotos_photo">
          <?php echo $this->htmlLink("javascript:he_show_image('{$photo->getPhotoUrl()}')", $this->itemPhoto($photo, 'thumb.normal'))  ?>
        </div>
        <div class="offers_editphotos_info">
          <?php
            $key = $photo->getGuid();
            echo $this->form->getSubForm($key)->render($this);
          ?>
    <div class="offers_editphotos_cover">
            <input type="radio" name="cover" value="<?php echo $photo->getIdentity() ?>" <?php if( $this->offer->photo_id == $photo->getIdentity() ): ?> checked="checked"<?php endif; ?> />
    </div>
    <div class="offers_editphotos_label">
            <label><?php echo $this->translate('OFFERS_Main Photo');?></label>
    </div>
        </div>
      </li>
    <?php endforeach; ?>

    <?php echo $this->form->submit->render(); ?>
</form>

<?php if( $this->paginator->count() > 0 ): ?>
  <br />
  <?php echo $this->paginationControl($this->paginator); ?>
<?php endif; ?>

<?php else: ?>

<div class="tip">
  <span>
    <?php echo $this->translate('No photos in this offer.');?>
  </span>
</div>

<?php endif; ?>