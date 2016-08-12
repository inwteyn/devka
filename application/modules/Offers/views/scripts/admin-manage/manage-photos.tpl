<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manage_photos.php 2012-07-03 11:40 ratbek $
 * @author     Ratbek
 */
?>

<script type="text/javascript">

en4.core.runonce.add(function() {
  if ($('form_notice') != null) {
    setTimeout(function() {
      $('form_notice').set('tween', {duration : 1000});
      $('form_notice').tween('opacity', 0);
    }, 3000);

    setTimeout(function() {
      $('form_notice').setStyle('display','none');
    }, 4000);
  }
});

function onSubmit()
{
  $('saved').setProperty('value', '1');
}
</script>

<h2><?php echo $this->translate("OFFERS_Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
  </div>
<?php endif; ?>

<br />
<div class="offers_admin_manage_menu">
  <a class="offers_admin_manage_menu_items" href="<?php echo $this->url(array('action' => 'index'), 'offer_admin_manage', true);?>"><?php echo $this->translate('OFFERS_View Offers'); ?></a>
  <a class="offers_admin_manage_menu_items" href="<?php echo $this->url(array('action' => 'create'), 'offer_admin_manage', true);?>"><?php echo $this->translate('OFFERS_Create Offer');?></a>
</div>

<h3 class="offers_admin_manage_photo_title">
  <?php echo $this->htmlLink($this->offer->getHref(), $this->offer->getTitle());
    echo ' ('. $this->translate(array('%s photo', '%s photos', $this->offer->count()),$this->locale()->toNumber($this->offer->count())) . ')';
  ?>
</h3>

<?php if ($this->message): ?>
  <ul class="form-notices" id="form_notice"><li><?php echo $this->message; ?></li></ul>
<?php endif; ?>

<?php if ($this->paginator->getTotalItemCount() > 0): ?>

  <?php if( $this->paginator->count() > 0 ): ?>
    <br />
    <?php echo $this->paginationControl($this->paginator); ?>
  <?php endif; ?>

  <form onsubmit="onSubmit()" id="offers-photo-manage" action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>">
    <?php if (!$this->created): ?>
      <div class="offers_manage_links">
        <?php echo $this->htmlLink($this->url(array('action'=>'edit', 'offer_id' => $this->offer->offer_id), 'offer_admin_manage'), $this->translate('OFFERS_Back'), array(
          'class' => 'buttonlink back_to_edit',
        )); ?>
        <br />
        <?php echo $this->htmlLink($this->url(array('action'=>'add-photos', 'offer_id' => $this->offer->offer_id), 'offer_admin_manage'), $this->translate('OFFERS_Add photos'), array(
          'class' => 'buttonlink add_photo',
        )); ?>
      </div>
    <?php endif; ?>
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

      <input type="hidden" id="saved" name="saved" value="0">

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
  <div class="no_photos_offer_manage_links">
    <?php echo $this->htmlLink($this->url(array('action'=>'edit', 'offer_id' => $this->offer->offer_id), 'offer_admin_manage'), $this->translate('OFFERS_Back'), array(
      'class' => 'buttonlink back_to_edit',
    )); ?>
    <br />
    <?php echo $this->htmlLink($this->url(array('action'=>'add-photos', 'offer_id' => $this->offer->offer_id), 'offer_admin_manage'), $this->translate('OFFERS_Add photos'), array(
      'class' => 'buttonlink add_photo',
    )); ?>
  </div>

<?php endif; ?>