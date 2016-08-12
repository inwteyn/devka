<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manage_photos.tpl 2010-09-06 17:53 idris $
 * @author     Idris
 */
?>

<h3>
  <?php echo $this->htmlLink("javascript:page_album.view({$this->album->getIdentity()})", $this->album->getTitle()) ?> (<?php echo $this->translate(array('%s photo', '%s photos', $this->album->count()),$this->locale()->toNumber($this->album->count())) ?>)
</h3>

<?php if ($this->paginator->getTotalItemCount() > 0): ?>

<?php if( $this->paginator->count() > 0 ): ?>
  <br />
  <?php echo $this->paginationControl($this->paginator); ?>
<?php endif; ?>

<form id="page-album-photo-manage" action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>">
  <?php echo $this->form->album_id; ?>
  <ul class='albums_editphotos'>
    <?php foreach( $this->paginator as $photo ): ?>
      <li>
        <div class="albums_editphotos_photo">
          <?php echo $this->htmlLink("javascript:he_show_image('{$photo->getPhotoUrl()}')", $this->itemPhoto($photo, 'thumb.normal'))  ?>
        </div>
        <div class="albums_editphotos_info">
          <?php
            $key = $photo->getGuid();
            echo $this->form->getSubForm($key)->render($this);
          ?>
    <div class="albums_editphotos_cover">
            <input type="radio" name="cover" value="<?php echo $photo->getIdentity() ?>" <?php if( $this->album->photo_id == $photo->getIdentity() ): ?> checked="checked"<?php endif; ?> />
    </div>
    <div class="albums_editphotos_label">
            <label><?php echo $this->translate('Album Cover');?></label>
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
    <?php echo $this->translate('No photos in this album.');?>
  </span>
</div>

<?php endif; ?>