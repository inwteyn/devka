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
					'filter_default_value'=>$this->translate('TOUCH_Search Classifieds'),
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

      <ul class="items classifieds_profile_tab">
        <?php foreach( $this->paginator as $item ): ?>
          <li>
            <div class='item_photo'>
              <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal'), array('class' => 'touchajax')) ?>
            </div>
            <div class='item_body classifieds_profile_tab_info'>
              <div class='classifieds_profile_tab_title'>
                <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'touchajax')) ?>
                <?php if( $item->closed ): ?>
                  <img src='application/modules/Classified/externals/images/close.png'/>
                <?php endif;?>
              </div>
              <div class='item_date'>
                <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
              </div>
              <div class='classifieds_browse_info_blurb'>
                <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 300) ?>
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