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
					'filter_default_value'=>$this->translate('TOUCH_Search Events'),
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
        <?php foreach( $this->paginator as $event ): ?>
          <li>
            <div class="item_photo">
              <?php echo $this->htmlLink($event, $this->itemPhoto($event, 'thumb.normal'), array('class' => 'touchajax')) ?>
            </div>
            <div class="item_body">
              <div>
                <?php echo $this->htmlLink($event->getHref(), $event->getTitle(), array('class' => 'touchajax')) ?>
              </div>
              <div>
                <?php echo $this->translate(array('%s guest', '%s guests', $event->member_count),$this->locale()->toNumber($event->member_count)) ?>
              </div>
              <div>
                <?php echo $this->touchSubstr($event->getDescription()); ?>
              </div>
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

<div class="touch_add_item">

  <?php if( $this->canAdd ): ?>
    <?php echo $this->htmlLink(array(
        'route' => 'event_general',
        'controller' => 'event',
        'action' => 'create',
        'parent_type'=> 'group',
        'subject_id' => $this->subject()->getIdentity(),
      ), $this->translate('Add Events'), array(
        'class' => 'buttonlink touch_new_event touchajax'
    )) ?>
  <?php endif; ?>

</div>