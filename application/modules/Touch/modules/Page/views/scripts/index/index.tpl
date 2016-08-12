<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
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
    'search'=>$this->formFilter->getElement('search')->getValue(),
    'filter_default_value'=>$this->translate('TOUCH_Search Pages'),
    'filterUrl'=>$this->url(array(), 'page_browse', true),
    )
    ); ?>
  </div>

  <div id="filter_block">

    <?php if( count($this->paginator) > 0 ): ?>

    <ul class='items'>
      <?php foreach( $this->paginator as $page ): ?>
      <?php $page_id = $page->getIdentity()?>

      <li class="<?php if ($page->featured) echo " active
      "; ?>">
      <div class="item_photo">
        <?php echo $this->htmlLink($page->getHref(), $this->itemPhoto($page, 'thumb.normal'), array('class' => 'touchajax')) ?>
      </div>
      <div class="item_body">

        <div class="item_title">

          <?php echo $this->htmlLink($page->getHref(), $page->getTitle(), array('class' => 'touchajax'))?>
          <?php if ($page->sponsored) : ?>
            <span class="page_item_featured"><?php echo $this->translate("Sponsored"); ?></span>
          <?php endif; ?>
          <?php if ($page->featured) : ?>
            <span class="page_item_featured"><?php echo $this->translate("Featured"); ?></span>
          <?php endif; ?>


        </div>
        <div class="item_date">
          <?php echo $this->translate("Submitted by"); ?>
          <a href="<?php echo $page->getOwner()->getHref(); ?>" class="touchajax"><?php echo $page->getOwner()->getTitle();
            ?></a>, <?php echo $this->translate("updated"); ?>
          <?php echo $this->timestamp($page->modified_date); ?> | <?php echo $page->view_count ?> <?php echo $this->
          translate("views"); ?>

          <br/>

          <?php if (!empty($page->category_id)): ?>
          <?php echo $this->translate("Category"); ?>:
          <?php echo $page->category; ?><br/>
          <?php endif; ?>
          <?php if (!empty($this->page_likes[$page_id])): ?>
          <?php echo $this->translate("Total Likes"); ?>:
          <?php echo $this->page_likes[$page_id]; ?><br/>
          <?php endif; ?>
        </div>

        <div class="item_desc">
          <?php echo $this->touchSubstr($page->getDescription()) ?>
        </div>

      </div>
      </li>
      <?php endforeach; ?>
    </ul>

    <?php else: ?>
    <div class="tip">
      <?php echo $this->translate('There is no pages.') ?>
    </div>
    <?php endif; ?>

  </div>
</div>
