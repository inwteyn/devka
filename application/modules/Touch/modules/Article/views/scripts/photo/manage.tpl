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
            ), $this->translate('Photos'), array(
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
      <?php echo $this->translate('Manage Photos');?>
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
        </div>
        <div class="item active" >
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
<?php if( $this->paginator->getTotalItemCount()): ?>

	<form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>" class="global_form articles_browse_filters">
	  <div>
	    <div>
	      <h3><?php echo $this->form->getTitle(); ?></h3>

	      <?php $notices = $this->form->getNotices(); ?>

	      <?php if (!empty($notices)): ?>
	        <ul class="form-notices">
	        <?php foreach ($notices as $notice): ?>
	          <li><?php echo $notice; ?></li>
	        <?php endforeach; ?>
	        </ul>
	      <?php endif; ?>
	      <ul class='articles_editphotos'>
	        <?php foreach( $this->paginator as $photo ): ?>
	          <li>
	            <div class="articles_editphotos_photo">
                <br />
                <br />
	              <?php echo $this->itemPhoto($photo, 'thumb.normal')  ?>
	            </div>
	            <div class="articles_editphotos_info">
	              <?php
	                $key = $photo->getGuid();
	                echo $this->form->getSubForm($key)->render($this);
	              ?>
	              <div class="articles_editphotos_cover">
	                <input id='cover_<?php echo $photo->getIdentity() ?>' type="radio" name="cover" value="<?php echo $photo->getIdentity() ?>" <?php if( $this->article->photo_id == $photo->file_id ): ?> checked="checked"<?php endif; ?> />
                  <label for='cover_<?php echo $photo->getIdentity() ?>' ><?php echo $this->translate('Main Photo');?></label>
	              </div>
                <hr />
	            </div>
	          </li>
	        <?php endforeach; ?>
	      </ul>
	      <?php echo $this->form->submit->render(); ?>

	    </div>
	  </div>
	</form>

<?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('This article does not have any photos.');?>
          <?php echo $this->translate("Get started by <a href='%1\$s'>uploading</a> a new photo.", $this->url(array('controller' => 'photo',
        'action' => 'upload',
        'subject' => $this->subject()->getGuid()), 'article_extended'));?>
      </span>
    </div>
<?php endif; ?>

</div>