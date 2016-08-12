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
					'filter_default_value'=>$this->translate('TOUCH_Search Photos'),
					'filterUrl'=> $this->url($urlParams, 'default', true),
          'filterOptions' => array(
            'replace_content' => 'widget_content',
            'noChangeHash' => 1,
          ),
          'pageUrlParams' => $urlParams
        )
		); ?>
  </div>

  <div id="filter_block" class="touch_box">

    <?php if ($this->paginator->getTotalItemCount()):?>

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

    <?php else :?>

      <div class="tip">
        <span><?php echo $this->translate('TOUCH_WIDGET_NOITEMS')?></span>
      </div>

	  <?php endif;?>
  </div>

  <div class="touch_add_item">
    <?php if( $this->canUpload ): ?>
      <?php echo $this->htmlLink(array(
          'route' => 'group_extended',
          'controller' => 'photo',
          'action' => 'upload',
          'subject' => $this->subject()->getGuid(),
        ), $this->translate('Upload Photos'), array(
          'class' => 'buttonlink touch_new_photo touchajax'
      )) ?>
    <?php endif; ?>
  </div>

</div>

