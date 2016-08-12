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
					'filter_default_value'=>$this->translate('TOUCH_Search Albums'),
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
        <?php foreach( $this->paginator as $album ): ?>
          <li>
            <div class="item_photo">
              <a href="<?php echo $album->getHref(); ?>">
                <img src="<?php echo $album->getPhotoUrl('thumb.normal'); ?>" width="60px"/>
              </a>
            </div>
            <div class="item_body">
              <span>
                <?php echo $this->htmlLink($album, $this->string()->chunk(Engine_String::substr($album->getTitle(), 0, 45), 10), array('class' => 'touchajax')) ?>
              </span>
              <div class="item_date">
                <?php echo $this->translate('By');?>
                <?php echo $this->htmlLink($album->getOwner()->getHref(), $album->getOwner()->getTitle(), array('class' => 'thumbs_author touchajax')) ?>
                -
                <?php echo $this->translate(array('%s photo', '%s photos', $album->count()),$this->locale()->toNumber($album->count())) ?>
              </div>
            </div>
          </li>
        <?php endforeach;?>
      </ul>

    <?php else :?>
      <div class="tip">
        <span><?php echo $this->translate('TOUCH_WIDGET_NOITEMS')?></span>
      </div>

	  <?php endif;?>
  </div>
</div>
