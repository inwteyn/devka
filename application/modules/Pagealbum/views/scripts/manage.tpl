<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manage.tpl 2010-09-06 17:53 idris $
 * @author     Idris
 */
?>

<?php if( $this->albums->getTotalItemCount() > 0 ): ?>
  <ul class='albums_manage'>
    <?php foreach( $this->albums as $album ): ?>
      <li>
        <div class="albums_manage_photo">
          <?php echo $this->htmlLink($album->getHref(),
                                     $this->itemPhoto($album, 'thumb.normal'),
                                     array('onclick'=>"page_album.view({$album->getIdentity()}); return false;"))
          ?>
        </div>
        <div class="albums_manage_info">
          <h3>
            <?php echo $this->htmlLink($album->getHref(), $album->getTitle(),
              array('onclick'=>"page_album.view({$album->getIdentity()}); return false;"))
            ?>
          </h3>
          <div class="albums_manage_info_photos">
            <?php echo $this->translate(array('%s photo', '%s photos', $album->count()),$this->locale()->toNumber($album->count())) ?>
          </div>
          <div class="albums_manage_info_desc">
            <?php echo Engine_String::substr(Engine_String::strip_tags($album->getDescription()), 0, 300); ?>
          </div>
        </div>
        <div class="albums_manage_options">
          <?php echo $this->htmlLink('javascript:void(0)', $this->translate('Manage Photos'), array(
            'class' => 'buttonlink icon_photos_manage',
            'id' => 'page_album_editphotos',
            'onClick' => 'page_album.manage_photos('.$album->getIdentity().');'
          )) ?>
          <?php echo $this->htmlLink('javascript:void(0)', $this->translate('Edit Album'), array(
            'class' => 'buttonlink icon_photos_settings',
            'id' => 'page_album_editsettings',
            'onClick' => 'page_album.edit('.$album->getIdentity().');'
          )) ?>
          <?php echo $this->htmlLink('javascript:void(0)', $this->translate('Delete Album'), array(
            'class' => 'buttonlink icon_photos_delete',
            'id' => 'page_album_delete',
            'onClick' => 'page_album.delete_album('.$album->getIdentity().');'
          )) ?>
        </div>
      </li>
    <?php endforeach; ?>
    <?php if( $this->albums->count() > 1 ): ?>
      <br />
      <?php echo $this->paginationControl($this->albums, null, array("pagination.tpl","pagealbum"), array(
        'page' => $this->pageObject
      ));?>
    <?php endif; ?>
  </ul>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('You do not have any albums yet.');?>
      <?php if ($this->isAllowedPost): // @todo check if user is allowed to create an album ?>
        <?php echo $this->translate('Get started by %1$screating%2$s your first album!', '<a href="javascript:page_album.create();">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>