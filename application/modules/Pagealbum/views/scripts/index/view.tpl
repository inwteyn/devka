<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2010-09-06 17:53 idris $
 * @author     Idris
 */
?>
<?php
if($this->photoviewer == 1){
 
?>
<div class="pagealbum_view_header with_photoviewer">
<span>
  <?php if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('page.show.owner', 0) == 1 || Engine_Api::_()->getItem('page', $this->album->page_id)->getOwner() != $this->album->getOwner()):?>
    <?php echo $this->translate('%1$s\'s Album: ', $this->album->getOwner()->__toString()); ?>
  <?php else:?>
    <?php echo $this->translate('%1$s\'s Album: ', $this->htmlLink(Engine_Api::_()->getItem('page', $this->album->page_id)->getHref(), Engine_Api::_()->getItem('page', $this->album->page_id)->getTitle())); ?>
  <?php endif;?>
  <?php echo $this->translate('%1$s', $this->album->getTitle()); ?>
</span>
<?php if (!$this->isAllowedPost): ?>
<div class="backlink_wrapper">
	<a class="backlink" href="javascript:page_album.list()"><?php echo $this->translate('Back To Albums'); ?></a>
</div>
<?php endif; ?>
<div class="clr"></div>
</div>
<?php if (""!=$this->album->getDescription()): ?>
  <p>
    <?php echo $this->album->getDescription() ?>
  </p>
<?php endif ?>
<div class="page-misc">
	<div class="clr"></div>
</div>
<div class="clr"></div>
<?php if ($this->mine || $this->can_edit):?>
  <div class="album_options">
    <?php echo $this->htmlLink('javascript:void(0)', $this->translate('Add More Photos'), array(
      'class' => 'buttonlink icon_photos_new',
      'onClick' => 'page_album.create('.$this->album->getIdentity().');'
    )) ?>
    <?php echo $this->htmlLink('javascript:void(0)', $this->translate('Manage Photos'), array(
      'class' => 'buttonlink icon_photos_manage',
      'onClick' => 'page_album.manage_photos('.$this->album->getIdentity().');'
    )) ?>
    <?php echo $this->htmlLink('javascript:void(0)', $this->translate('Edit Album'), array(
      'class' => 'buttonlink icon_photos_settings',
      'onClick' => 'page_album.edit('.$this->album->getIdentity().');'
    )) ?>
    <?php echo $this->htmlLink('javascript:void(0)', $this->translate('Delete Album'), array(
      'class' => 'buttonlink smoothbox icon_photos_delete',
      'onClick' => 'page_album.delete_album('.$this->album->getIdentity().');'
    )) ?>
  </div>
	<div class="clr"></div>
<?php endif;?>
<?php if ($this->paginator->getTotalItemCount() > 0): ?>
<div class="layout_middle">
  <script type="text/javascript">
    function viewMore()
    {
      hapPhotos.loadMore(function (res) {
        if (!res.is_next) {
          $('viewMore').hide();
        }
      });
    }
      (new HapInstance({
        request_url: '<?php echo $this->url();?>?format=json',
        loading_on_scroll: false
      }));
  </script>
<div id="gallery" class="ad-gallery">
  <div class="hapLoader" id="hapLoader"></div>
  <div class="hapLoader" id="hapBuildLoader"></div>
  <div class="ad-nav">
    <div class="ad-thumbs">
      <ul class="hapPhotos" id="hapPhotos">
      <?php $counter = 0; ?>
      <?php foreach( $this->paginator as $photo ): ?>
        <?php
        if (isset($this->photos[$key])){
          $photo = $this->photos[$key];
        }
        $owner = $photo->getOwner();
        ?>
        <img id="image_<?php echo $photo->getIdentity(); ?>" src="<?php echo $photo->getPhotoUrl('thumb.normal'); ?>" border="0" style="display:none" class="<?php echo "image".$counter++; ?>" />
        <li id="<?php echo $id_prefix;?>photo_<?php echo $photo->getGuid();?>" style="visibility: hidden;"> <!--  visibility: hidden; waiting rebuild -->
          <div class="photo">
              <a href="<?php echo $photo->getHref();?>" class="aimg">
                <img src="<?php echo $photo->getPhotoUrl();?>" class="img" alt="" id="<?php echo $id_prefix;?>img_<?php echo $photo->getGuid();?>"/>
              </a>
          </div>
          <div class="caption">
            <div class="content">
              <?php echo $this->translate('by');?>&nbsp;<a href="<?php echo $owner->getHref();?>"><?php echo $owner->getTitle();?></a>
            </div>
          </div>
          <div class="hover-caption">
            <div class="content">
              <div class="title">
                <?php
                $title = $photo->getTitle();
                if (empty($title)){
                  if (method_exists($photo, 'getAlbum')){
                    $title = $photo->getAlbum()->getTitle();
                  } else if (method_exists($photo, 'getCollection')){
                    $title = $photo->getCollection()->getTitle();
                  }
                }
                ?>
                <?php echo $title;?><br />
                <?php echo $this->translate('by');?>&nbsp;<a href="<?php echo $owner->getHref();?>"><?php echo $owner->getTitle();?></a>
              </div>
              <div class="info">
                <span class="comment-count"><i class="hei hei-thumbs-up"></i> <?php echo $photo->likes()->getLikeCount();?></span>
                <span class="like-count"><i class="hei hei-comment"></i> <?php echo $photo->comments()->getCommentCount();?></span>
              </div>
            </div>
          </div>
        </li>
      <?php endforeach;?>
      </ul>
    </div>
  </div>
</div>
  </div>
<div class="btn_cont_load_photo_comments">
  <a id="load_photo_comments" class="hidden" href="javascript:void(0)"><?php echo $this->translate("Comments"); ?></a>
</div>
<div class="clr"></div>
  <?php if ($this->is_next):?>
    <a href="javascript:void(0);" onclick="viewMore();" id="viewMore"><?php echo $this->translate('HEADVANCEDALBUM_SEE_MORE');?></a>
  <?php endif;?>
<?php else: ?>
<div class="tip">
  <span>
    <?php echo $this->translate('No photos in this album.');?>
  </span>
</div>
<?php endif; ?>
<br/>
<br/>
<?php
}else{

  ?>
<div class="pagealbum_view_header">

<span>
  <?php if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('page.show.owner', 0) == 1 || Engine_Api::_()->getItem('page', $this->album->page_id)->getOwner() != $this->album->getOwner()):?>
    <?php echo $this->translate('%1$s\'s Album: ', $this->album->getOwner()->__toString()); ?>
  <?php else:?>
    <?php echo $this->translate('%1$s\'s Album: ', $this->htmlLink(Engine_Api::_()->getItem('page', $this->album->page_id)->getHref(), Engine_Api::_()->getItem('page', $this->album->page_id)->getTitle())); ?>
  <?php endif;?>
  <?php echo $this->translate('%1$s', $this->album->getTitle()); ?>
</span>

<?php if (!$this->isAllowedPost): ?>
<div class="backlink_wrapper">
	<a class="backlink" href="javascript:page_album.list()"><?php echo $this->translate('Back To Albums'); ?></a>
</div>
<?php endif; ?>

<div class="clr"></div>
</div>

<?php if (""!=$this->album->getDescription()): ?>
  <p>
    <?php echo $this->album->getDescription() ?>
  </p>
<?php endif ?>

<div class="page-misc">
	<div class="page-misc-date">
		<?php echo $this->translate("Posted %s", $this->timestamp($this->album->creation_date)); ?>
	</div>
	<?php if (count($this->albumTags)):?>
	<div class="page-tag">
		<div class="tags">
      <?php foreach ($this->albumTags as $tag): ?>
        <a href='javascript:void(0);' onclick="page_search.search_by_tag(<?php echo $tag->getTag()->tag_id; ?>);">#<?php echo $tag->getTag()->text ?></a>&nbsp;
      <?php endforeach; ?>
		</div>
		<div class="clr"></div>
	</div>
	<?php endif; ?>
	<div class="clr"></div>
</div>
<div class="clr"></div>

<?php if ($this->mine || $this->can_edit):?>
  <div class="album_options">
    <?php echo $this->htmlLink('javascript:void(0)', $this->translate('Add More Photos'), array(
      'class' => 'buttonlink icon_photos_new',
      'onClick' => 'page_album.create('.$this->album->getIdentity().');'
    )) ?>
    <?php echo $this->htmlLink('javascript:void(0)', $this->translate('Manage Photos'), array(
      'class' => 'buttonlink icon_photos_manage',
      'onClick' => 'page_album.manage_photos('.$this->album->getIdentity().');'
    )) ?>
    <?php echo $this->htmlLink('javascript:void(0)', $this->translate('Edit Album'), array(
      'class' => 'buttonlink icon_photos_settings',
      'onClick' => 'page_album.edit('.$this->album->getIdentity().');'
    )) ?>
    <?php echo $this->htmlLink('javascript:void(0)', $this->translate('Delete Album'), array(
      'class' => 'buttonlink smoothbox icon_photos_delete',
      'onClick' => 'page_album.delete_album('.$this->album->getIdentity().');'
    )) ?>
  </div>
	<div class="clr"></div>
<?php endif;?>

<?php if ($this->paginator->getTotalItemCount() > 0): ?>

<div class="layout_middle">

<script type="text/javascript">
$jq(function(){
	<?php $counter = 0; $str = ''; ?>
	<?php foreach( $this->paginator as $photo ): ?>
	<?php $str .= "page_album.photos.push({$photo->getIdentity()});"; ?>
    $jq('img.image<?php echo $counter; ?>').data('ad-desc', <?php echo Zend_Json_Encoder::encode($photo->getDescription()); ?>);
    $jq('img.image<?php echo $counter; ?>').data('ad-title', <?php echo Zend_Json_Encoder::encode($photo->getTitle()); ?>);
    <?php $counter++; ?>
  <?php endforeach; ?>

  galleries = $jq('.ad-gallery').adGallery();
  galleries[0].settings.effect = 'fade';
  galleries[0].settings.loader_image = '<?php echo $this->baseUrl()."/application/modules/Pagealbum/externals/images/loader.gif"; ?>';
	<?php if ($this->startIndex): ?>
		galleries[0].settings.start_at_index = <?php echo (int)$this->startIndex; ?>;
		galleries[0].showImage(<?php echo (int)$this->startIndex; ?>, function(){});
	<?php endif; ?>
});

<?php $comments_html = $this->render('comment/list.tpl'); ?>

<?php echo $str; ?>
page_album.photo_comments['comments_<?php echo (int)$this->startIndex; ?>'] = <?php echo Zend_Json_Encoder::encode($comments_html); ?>;
</script>

<div id="gallery" class="ad-gallery">
  <div class="ad-image-wrapper">
  </div>
  <div class="ad-controls">
  </div>
  <div class="ad-nav">
    <div class="ad-thumbs">
      <ul class="ad-thumb-list">
      <?php $counter = 0; ?>
      <?php foreach( $this->paginator as $photo ): ?>
	      <li>
	        <a href="<?php echo $photo->getPhotoUrl(); ?>" class="thumbs_photo" href="javascript:page_album.view_photo(<?php echo $photo->getIdentity(); ?>)">
	          <img id="image_<?php echo $photo->getIdentity(); ?>" src="<?php echo $photo->getPhotoUrl('thumb.normal'); ?>" border="0" class="<?php echo "image".$counter++; ?>" />
	        </a>
	      </li>
      <?php endforeach;?>
      </ul>
    </div>
  </div>
</div>

</div>

<div class="btn_cont_load_photo_comments">
  <a id="load_photo_comments" class="hidden" href="javascript:void(0)"><?php echo $this->translate("Comments"); ?></a>
</div>
<div class="clr"></div>

<div class="comments" id="photo_comments_container"></div>

<?php else: ?>

<div class="tip">
  <span>
    <?php echo $this->translate('No photos in this album.');?>
  </span>
</div>

<?php endif; ?>

<br/>
<br/><?php
}
  ?>