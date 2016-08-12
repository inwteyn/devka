<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-02-20 15:35 Ulan T $
 * @author     Ulan T
 */
?>

<ul class="generic_list_widget generic_list_widget_large_photo">
  <?php foreach( $this->paginator as $item ): ?>
  <?php
  if( $item['type'] == 'page' )
    $album = Engine_Api::_()->getItem('pagealbum', $item['album_id']);
  else
    $album = Engine_Api::_()->getItem('album', $item['album_id']);
  ?>

  <li>
    <div class="photo">
      <a class="thumb_photo"  href="<?php echo $album->getHref();?>">
        <span style="background-image: url(<?php echo $album->getPhotoUrl('thumb.normal'); ?>);"></span>
      </a>
    </div>
    <div class="info">
      <div class="title">
        <?php echo $this->htmlLink($album->getHref(), $this->string()->truncate($album->getTitle(), 13)) ?>
      </div>
      <div class="stats">
        <?php echo $this->timestamp($album->creation_date) ?>
      </div>
      <div class="owner">
        <?php
        $owner = $album->getOwner();?>
        <?php if( $item['type'] == 'page' && (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('page.show.owner', 0) == 1 || Engine_Api::_()->getItem('page', $album->page_id)->getOwner() != $album->getOwner())) : ?>
          <?php echo $this->translate('Posted by %1$s', $this->htmlLink($owner->getHref(), $owner->getTitle()));?>
        <?php elseif ($item['type'] == 'page'):?>
          <?php echo $this->translate('Posted by %1$s', $this->htmlLink(Engine_Api::_()->getItem('page', $album->page_id)->getHref(), Engine_Api::_()->getItem('page', $album->page_id)->getTitle()));?>
        <?php else:?>
          <?php echo $this->translate('Posted by %1$s', $this->htmlLink($owner->getHref(), $owner->getTitle()));?>
        <?php endif;?>
        <?php
        if( $item['type'] == 'page' ) {
          echo '<br/>';
          echo $this->translate('On page ');
          echo $this->htmlLink($album->getPage()->getHref(), $album->getPage()->getTitle());
        }
        ?>
      </div>
    </div>
  </li>
  <?php endforeach; ?>
</ul>