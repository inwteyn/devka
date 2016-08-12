<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: create.tpl 2011-07-22 11:18:13 ulan $
 * @author     Ulan
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
<div class="touch-navigation">
  <div class="navigation-header navigation-paginator">
    <h3 style="line-height: 30px;">
      <a href="<?php echo $this->article->getHref() ?>" class="touchajax"><?php echo $this->article->getTitle(); ?></a>
      <?php echo $this->translate('&#187; ').
      $this->htmlLink(array(
              'route' => 'article_extended',
              'controller' => 'photo',
              'action' => 'list',
              'subject' => $this->article->getGuid(),
            ), $this->translate('View Photos'), array(
              'class' => 'touchajax'
          )); ?>
    </h3>
    <span class="touch-navigation-paginator">
      <?php if (isset($this->paginator->getPages()->previous)): ?>
        <a	class="paginator-navigation" href="<?php echo $this->url(array('page' => $this->paginator->getPages()->previous)); ?>" onclick="Touch.navigation.request($(this)); return false;">
            <img src="application/modules/Touch/themes/<?php echo $this->touchActiveTheme(); ?>/images/prev.png" alt="<?php echo $this->translate('Prev') ?>" />
        </a>
      <?php else: ?>
        <span class="paginator-navigation"><img src="application/modules/Touch/themes/<?php echo $this->touchActiveTheme(); ?>/images/prev_disabled.png" alt="<?php echo $this->translate('Prev') ?>" /></span>
      <?php endif; ?>

      <?php if (isset($this->paginator->getPages()->next)): ?>
        <a class="paginator-navigation" href="<?php echo $this->url(array('page' => $this->paginator->getPages()->next)); ?>" onclick="Touch.navigation.request($(this)); return false;">
          <img src="application/modules/Touch/themes/<?php echo $this->touchActiveTheme(); ?>/images/next.png" alt="<?php echo $this->translate('Prev') ?>" />
        </a>
      <?php else: ?>
        <span class="paginator-navigation"><img src="application/modules/Touch/themes/<?php echo $this->touchActiveTheme(); ?>/images/next_disabled.png" alt="<?php echo $this->translate('Next') ?>"/></span>
      <?php endif; ?>
    </span>
    <div id="navigation-selector">
        <?php echo $this->translate('View Photos') ?>
    </div>
    <div class="navigation-body">
      <div id="navigation-items">
        <div class="item" >
          <?php echo $this->htmlLink($this->article, ($this->translate('TOUCH_ARTICLE_BACK_TO_ARTICLE').' '.$this->translate('"').$this->article->getTitle().$this->translate('"')), array("class" => "touchajax")); ?>
        </div>
        <div class="item active" >
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
<!--        <div class="item" >-->
<!--          --><?php //echo $this->htmlLink(array(
//              'route' => 'article_extended',
//              'controller' => 'photo',
//              'action' => 'upload',
//              'subject' => $this->article->getGuid(),
//            ), $this->translate('Upload Photos'), array(
//              'class' => 'touchajax'
//          )) ?>
<!--        </div>-->
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<div style="height:10px"></div>
<div id="navigation_loading" style="display:none; text-align: center; vertical-align: middle;">
    <a class="buttonlink loader"><?php echo $this->translate("Loading"); ?>...</a>
  </div>
<div id="navigation_content">
    <div id="layout_content">
    <ul class="items">
      <?php foreach( $this->paginator as $photo ): ?>
        <li class="thumbs">
            <div class="item_photo">
              <a class="thumbs_photo touchajax" href="<?php echo $photo->getHref(); ?>">
                <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
              </a>
            </div>
        </li>
      <?php endforeach;?>
    </ul>
    <div class="clr"></div>
  </div>
  </div>
