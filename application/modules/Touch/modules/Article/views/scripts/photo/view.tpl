<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>
<script type="text/javascript">
(function(){
	var options={
		prev_button: 'paginator-navigation-prev',
		next_button: 'paginator-navigation-next',
		media_photo: 'media_photo_next'
	}

	en4.core.runonce.add(function(){
		Photobox.setOptions(options);

		if (Photobox.isOpen){
			Photobox.show('media_photo');
		}
	});
})();
</script>

<div class="  touch-navigation">
	<div class="navigation-header navigation-paginator">
    <h3 style="line-height: 30px;" >
      <a  href="<?php echo $this->article->getHref() ?>" class="touchajax" ><?php echo $this->article->getTitle(); ?></a>
        <?php echo $this->translate('&#187; ').
          $this->htmlLink(array(
          'route' => 'article_extended',
          'controller' => 'photo',
          'action' => 'list',
          'subject' => $this->article->getGuid(),
          ), $this->translate('Photo Gallery'), array(
          'class' => 'touchajax'
        ));
       ?>
    </h3>

    <?php if ($this->album->count() > 1): ?>
    <span class="touch-navigation-paginator">
      <a class="paginator-navigation touchajax" onclick="Touch.navigation.request($(this)); return false;" href="<?php echo $this->photo->getPrevCollectible()->getHref(); ?>" id="paginator-navigation-prev"
         >
        <img src="application/modules/Touch/themes/<?php echo $this->touchActiveTheme(); ?>/images/prev.png" title="<?php echo $this->translate('Prev') ?>" alt="<?php echo $this->translate('Prev') ?>" />
      </a>

      <a class="paginator-navigation touchajax" onclick="Touch.navigation.request($(this)); return false;" href="<?php echo $this->photo->getNextCollectible()->getHref(); ?>"  id="paginator-navigation-next"
         >
        <img src="application/modules/Touch/themes/<?php echo $this->touchActiveTheme(); ?>/images/next.png" title="<?php echo $this->translate('Next') ?>" alt="<?php echo $this->translate('Next') ?>" />
      </a>
    </span>
    <?php endif; ?>
		<div id="navigation-selector">
				<?php echo ( '' != trim($this->photo->getTitle()) ? $this->touchSubstr($this->photo->getTitle(), 25) : $this->translate('TOUCH_ARTICLE_UNTITLED_PHOTO')); ?>
		</div>
		<div class="navigation-body">
      <div id="navigation-items">
        <div class="item" >
          <?php echo $this->htmlLink($this->article, ($this->translate('TOUCH_ARTICLE_BACK_TO_ARTICLE').' '.$this->translate('"').$this->article->getTitle().$this->translate('"')), array("class" => "touchajax")); ?>
        </div>
        <div class="item" >
          <?php echo $this->htmlLink(array(
              'route' => 'article_extended',
              'controller' => 'photo',
              'action' => 'list',
              'subject' => $this->article->getGuid(),
            ), $this->translate('View Photos'), array(
              'class' => 'touchajax'
          )) ?>
          <?php if( $this->canUpload ): ?>
        </div>
        <div class="item" >
          <?php echo $this->htmlLink(array(
              'route' => 'article_extended',
              'controller' => 'photo',
              'action' => 'manage',
              'subject' => $this->article->getGuid(),
            ), $this->translate('Manage Photos'), array(
              'class' => 'touchajax'
          )) ?>
        </div>
        <div class="item" >
          <?php echo $this->htmlLink(array(
              'route' => 'article_extended',
              'controller' => 'photo',
              'action' => 'upload',
              'subject' => $this->article->getGuid(),
            ), $this->translate('Upload Photos'), array(
              'class' => 'touchajax'
          )) ?>
        <?php endif; ?>
        </div>
      </div>
		</div>
	</div>
</div>
<div style="height:10px"></div>
<div id="navigation_loading" style="display:none; text-align: center; vertical-align: middle;">
	<a class="buttonlink loader"><?php echo $this->translate("Loading"); ?>...</a>
</div>
<div id="navigation_content">
	<div class="layout_content">
      <div class="album_photo_left">
        <?php echo $this->translate('Added');?> <?php echo $this->timestamp($this->photo->creation_date) ?>
        <?php echo $this->translate('By')?> <?php echo $this->htmlLink($this->photo->getOwner()->getHref(), $this->photo->getOwner()->getTitle()) ?>
    </div>
      <div class="album_photo_right">
        <?php echo $this->translate('Photo');?> <?php echo $this->photo->getCollectionIndex() + 1 ?>
        <?php echo $this->translate('of');?> <?php echo $this->album->count() ?>
    </div>

			<div class="clr"></div>

			<div class='photo_view_container'>
        <?php if( $this->photo->getTitle() ): ?>
          <h3 class="article_photo_title">
            <?php echo $this->photo->getTitle(); ?>
          </h3>
        <?php endif; ?>

				<div class='album_viewmedia_container photo' id='media_photo_div'>
          <a id='media_photo_next'  href='<?php echo $this->photo->getNextCollectible()->getHref() ?>'>
            <?php echo $this->htmlImage($this->photo->getPhotoUrl(), $this->photo->getTitle(), array(
              'id' => 'media_photo'
            )); ?>
          </a>
				</div>
        <?php if( $this->photo->getDescription() ): ?>
            <p>
              <?php echo nl2br($this->photo->getDescription()); ?>
            </p>
        <?php endif; ?>
        <?php if( $this->canUpload ): ?>
          <?php echo $this->htmlLink(array('route' => 'article_extended', 'controller' => 'photo', 'action' => 'edit', 'photo_id' => $this->photo->getIdentity(), 'format' => 'smoothbox'), $this->translate('Edit'), array('class' => 'smoothbox')) ?>
          - <?php echo $this->htmlLink(array('route' => 'article_extended', 'controller' => 'photo', 'action' => 'delete', 'photo_id' => $this->photo->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete'), array('class' => 'smoothbox')) ?>
        <?php endif; ?>

        - <?php echo $this->htmlLink(Array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>$this->photo->getType(), 'id'=>$this->photo->getIdentity(), 'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'smoothbox')); ?>
			</div>
      <?php echo $this->touchAction("list", "comment", "core", array("type"=>"article_photo", "id"=>$this->photo->getIdentity())); ?>
		</div>
</div>
