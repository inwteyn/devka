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
					'filter_default_value'=>$this->translate('TOUCH_Search Groups'),
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
        <?php foreach( $this->paginator as $group ): ?>
          <li>
            <div class="item_photo">
              <?php echo $this->htmlLink($group, $this->itemPhoto($group, 'thumb.normal'), array('class' => 'touchajax')) ?>
            </div>
            <div class="item_body">
              <div class="item_title">
                <?php echo $this->htmlLink($group->getHref(), $group->getTitle(), array('class' => 'touchajax')) ?>
              </div>
              <div class="item_date">
                <?php echo $this->translate(array('%s member', '%s members', $group->member_count),$this->locale()->toNumber($group->member_count)) ?>
              </div>
              <div class="item_desc">
                <?php echo $this->touchSubstr($group->getDescription()); ?>
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
