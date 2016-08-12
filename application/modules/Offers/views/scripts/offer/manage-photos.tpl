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
<div class="offers_navigation_editor tabs">
  <?php echo $this->navigation()->menu()->setContainer($this->navigation_edit)->setPartial(array('_navIcons.tpl', 'core'))->render(); ?>
</div>
<h2><?php echo $this->translate("OFFERS_manage_photos"); ?></h2>
<h3 class="offers_manage_photo_title">
  <?php echo $this->htmlLink($this->offer->getHref(), $this->offer->getTitle());
  echo ' ('. $this->translate(array('%s photo', '%s photos', $this->offer->count()),$this->locale()->toNumber($this->offer->count())) . ')';
  ?>
</h3>

<?php if ($this->message): ?>
<ul class="form-notices" id="form_notice"><li><?php echo $this->message; ?></li></ul>

<?php endif; ?>

<?php if ($this->paginator->getTotalItemCount() > 0): ?>

<?php if( $this->paginator->count() > 0 ): ?>
  <?php echo $this->paginationControl($this->paginator); ?>
  <?php endif; ?>

<form  id="offers-photo-manage" method="post">
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

<?php endif; ?>