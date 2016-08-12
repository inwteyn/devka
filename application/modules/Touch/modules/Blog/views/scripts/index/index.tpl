<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-04-26 11:18:13 mirlan $
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
    'search'=>$this->form->getElement('search')->getValue(),
    'filter_default_value'=>$this->translate('TOUCH_Search Blogs'),
    'filterUrl'=>$this->url(array(), 'blog_general', true)
    )
    ); ?>
  </div>

  <div id="filter_block">

    <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <ul class="items">
      <?php foreach( $this->paginator as $item ): ?>
      <li>
        <div class='item_photo'>
          <?php echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon'),
          array('class' => 'touchajax')) ?>
        </div>

        <div class='item_body'>
          <p class='blogs_browse_info_title'>
            <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'touchajax')) ?>
          </p>

          <div class='item_date'>
            <?php echo $this->translate('Posted');?>
            <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
            <?php echo $this->translate('by');?>
            <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle(), array('class' =>
            'touchajax')) ?>
          </div>
        </div>

        <div class="rate_blog_item">
          <?php echo $this->touchItemRate('blog', $item->getIdentity()); ?>
        </div>

      </li>
      <?php endforeach; ?>
    </ul>

    <?php elseif( $this->search ):?>
    <div class="tip">
        <span>
          <?php echo $this->translate('Nobody has written a blog entry with that criteria.');?>
          <?php if( $this->canCreate ): ?>
          <?php echo $this->translate('Be the first to %1$swrite%2$s one!', '<a
          href="'.$this->url(array('action' => 'create'), 'blog_general').'" class="touchajax">', '</a>'); ?>
          <?php endif; ?>
        </span>
    </div>

    <?php else:?>
    <div class="tip">
        <span>
          <?php echo $this->translate('Nobody has written a blog entry yet.'); ?>
          <?php if( $this->canCreate ): ?>
          <?php echo $this->translate('Be the first to %1$swrite%2$s one!', '<a
          href="'.$this->url(array('action' => 'create'), 'blog_general').'" class="touchajax">', '</a>'); ?>
          <?php endif; ?>
        </span>
    </div>
    <?php endif; ?>

  </div>
</div>


