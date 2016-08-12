<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */
?>
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <div class='albums_manage'>
    <?php foreach( $this->paginator as $item ): ?>
      <?php
      if($item['type'] == 'page')
        $album = Engine_Api::_()->getItem('pagealbum', $item['album_id']);
      else
        $album = Engine_Api::_()->getItem('album', $item['album_id']);
      ?>
      <div class="pagealbum_manage_item">
        <div class="pagealbum_manage_photo">
          <a class="thumb_photo" href="<?php echo $album->getHref();?>">
            <span style="background-image: url(<?php echo $album->getPhotoUrl('thumb.normal'); ?>);"></span>
          </a>
        </div>

        <div class="pagealbum_manage_info">
          <?php echo $this->htmlLink($album, $this->string()->chunk($this->string()->truncate($album->getTitle(), 45), 10)) ?>
          <div class="info">
            <?php echo $this->translate('By');?>
            <?php echo $this->htmlLink($album->getOwner()->getHref(), $album->getOwner()->getTitle()); ?>
            <?php if( $item['type'] == 'page') : ?>
              <br />
              <?php echo $this->translate('On page ') ?>
              <?php echo $this->htmlLink($album->getPage()->getHref(), $album->getPage()->getTitle()) ?>
            <?php endif;?>
            <br/>
            <?php echo $this->translate(array('%s photo', '%s photos', $album->count()),
              $this->locale()->toNumber($album->count())) ?>
            -
            <?php echo $album->view_count;?>
            <?php echo $this->translate('views')?>
          </div>
        </div>

      </div>
    <?php endforeach; ?>
    <?php if( $this->paginator->count() > 1 ): ?>
      <br />
      <?php echo $this->paginationControl(
        $this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->searchParams
      )); ?>
    <?php endif; ?>
  </div>
<?php else: ?>
  <div class="tip">
      <span>
        <?php echo $this->translate('There is no any albums.');?>
      </span>
  </div>
<?php endif; ?>
