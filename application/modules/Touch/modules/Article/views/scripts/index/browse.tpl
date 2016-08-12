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
<?php if( count($this->navigation) > 0 ): ?>
<?php
		// Render the menu
		echo $this->navigation()
->menu()
->setContainer($this->navigation)
->setPartial(array('navigation/index.tpl', 'touch'))
->render();
?>
<?php endif; ?>

<div id="navigation_content">

  <div class="search">
    <?php echo $this->paginationControl(
    $this->paginator,
    null,
    array('pagination/filter.tpl', 'touch'),
    array(
    'search'=>$this->version >= '4.1.0'?$this->form->getElement('keyword')->getValue():$this->form->getElement('search')->getValue(),
    'filter_default_value'=>$this->translate('TOUCH_ARTICLE_SEARCH'),
    'filterUrl'=>$this->url(array(), 'article_browse', true)
    )
    ); ?>
  </div>

  <div id="filter_block">
    <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <ul class="items">
      <?php foreach( $this->paginator as $item ): ?>
      <li>
        <div class='item_photo'>
          <?php echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item, 'thumb.icon'),
          array('class' => 'touchajax')) ?>
        </div>

        <div class='item_body'>
          <p class='article_browse_info_title'>
            <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'touchajax')) ?>
            <?php if( $item->featured ): ?>
              <img src='application/modules/Touch/modules/Article/externals/images/featured.png' class='article_title_icon_featured' />
            <?php endif;?>
            <?php if( $item->sponsored ): ?>
              <img src='application/modules/Touch/modules/Article/externals/images/sponsored.png' class='article_title_icon_sponsored' />
            <?php endif;?>
          </p>

          <div class='item_date'>
            <?php echo $this->translate('Posted');?>
            <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
            <?php echo $this->translate('by');?>
            <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle(), array('class' =>
            'touchajax')) ?>
              - <?php echo $this->translate(array("%s view", "%s views", $item->view_count), $this->locale()->toNumber($item->view_count)); ?>
              - <?php echo $this->translate(array("%s comment", "%s comments", $item->comment_count), $this->locale()->toNumber($item->comment_count)); ?>
              - <?php echo $this->translate(array('%1$s like', '%1$s likes', $item->like_count), $this->locale()->toNumber($item->like_count)); ?>
          </div>
        </div>

        <?php
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('touch.article.rate-browse', 1)){ ?>
        <div class="rate_article_item">
          <?php echo $this->touchItemRate('article', $item->getIdentity()); ?>
        </div>

        <?php } ?>

      </li>
      <?php endforeach; ?>
    </ul>

    <?php elseif('4.1.0'?$this->form->getElement('keyword')->getValue():$this->form->getElement('search')->getValue()):?>
    <div class="tip">
        <span>
          <?php echo $this->translate('Nobody has posted an article with that criteria.');?>
          <?php if( $this->canCreate ): ?>
          <?php echo $this->translate('Be the first to %1$swrite%2$s one!', '<a
          href="'.$this->url(array('action' => 'create'), 'article_browse').'" class="touchajax">', '</a>'); ?>
          <?php endif; ?>
        </span>
    </div>

    <?php else:?>
    <div class="tip">
        <span>
          <?php echo $this->translate('Nobody has posted an article yet.'); ?>
          <?php if( $this->canCreate ): ?>
          <?php echo $this->translate('Be the first to %1$swrite%2$s one!', '<a
          href="'.$this->url(array('action' => 'create'), 'article_browse').'" class="touchajax">', '</a>'); ?>
          <?php endif; ?>
        </span>
    </div>
    <?php endif; ?>

  </div>
</div>
