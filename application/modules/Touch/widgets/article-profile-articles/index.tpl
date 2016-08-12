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


$urlParams = array(
  'module' => 'core',
  'controller' => 'widget',
  'action' => 'index',
  'content_id' => $this->identity,
  'subject' => $this->subject()->getGuid(),
  'format' => 'html'
);

?>

<div id="widget_content">

	<div class="search">

		<?php echo $this->paginationControl($this->paginator, null,
				array('pagination/filter.tpl', 'touch'),
				array(
					'search'=>$this->form->getElement('search')->getValue(),
					'filter_default_value'=>$this->translate('TOUCH_Search Articles'),
					'filterUrl'=> $this->url($urlParams, 'default', true),
          'filterOptions' => array(
            'replace_content' => 'widget_content',
            'noChangeHash' => 1,
          ),
          'pageUrlParams' => $urlParams
        )
		); ?>
  </div>

  <div id="filter_block">

    <?php if ($this->paginator->getTotalItemCount()):?>

      <ul class="items">
          <?php foreach ($this->paginator as $item): ?>
            <li>
              <div class='item_body'>
                <p>
                  <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'touchajax')) ?>
                  <?php if( $item->featured ): ?>
                    <img src='application/modules/Touch/modules/Article/externals/images/featured.png' class='article_title_icon_featured' />
                  <?php endif;?>
                  <?php if( $item->sponsored ): ?>
                    <img src='application/modules/Touch/modules/Article/externals/images/sponsored.png' class='article_title_icon_sponsored' />
                  <?php endif;?>
                </p>
                <p class='item_date'>
                  <?php echo $this->translate('posted by %s', $item->getOwner()->__toString());?> <?php echo $this->timestamp($item->creation_date) ?>
                  <br /><?php echo $this->translate(array("%s view", "%s views", $item->view_count), $this->locale()->toNumber($item->view_count)); ?>
                  - <?php echo $this->translate(array("%s comment", "%s comments", $item->comment_count), $this->locale()->toNumber($item->comment_count)); ?>
                  - <?php echo $this->translate(array('%1$s like', '%1$s likes', $item->like_count), $this->locale()->toNumber($item->like_count)); ?>
                </p>
                <p>
                  <?php echo $this->touchSubstr(strip_tags($item->body)); ?>
                </p>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>

      <?php else :?>

      <div class="tip">
        <span><?php echo $this->translate('TOUCH_WIDGET_NOITEMS')?></span>
      </div>

	  <?php endif;?>
  </div>
</div>
