<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manage.tpl 2011-04-26 11:18:13 mirlan $
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
					'filter_default_value'=>$this->translate('TOUCH_Search Classifieds'),
					'filterUrl'=>$this->url(array('action'=> 'manage'), 'classified_general', true)
				)
		); ?>
	</div>

	<div id="filter_block">

  <?php if (($this->current_count >= $this->quota) && !empty($this->quota)):?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You have already created the maximum number of listings allowed. If you would like to create a new listing, please delete an old one first.');?>
      </span>
    </div>
    <br/>
  <?php endif; ?>
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <ul class="items classifieds_profile_tab">
      <?php foreach( $this->paginator as $item ): ?>
        <li>
          <div class='item_photo'>
            <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal'), array('class' => 'touchajax')) ?>
          </div>
          <div class='item_body'>

            <div class='classifieds_browse_info_title'>
              <div>
                <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'touchajax')) ?>
                <?php if( $item->closed ): ?>
                  <img alt="close" src='application/modules/Classified/externals/images/close.png'/>
                <?php endif;?>
              </div>
            </div>
            <div class='item_date classifieds_browse_info_date'>
              <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
              -
              <?php echo $this->translate('posted by');?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle(), array('class' => 'touchajax')) ?>

            - <?php echo $this->htmlLink(array(
              'route' => 'classified_specific',
              'action' => 'edit',
              'classified_id' => $item->getIdentity(),
            ), $this->translate('Edit'), array(
              'class' => 'touchajax'
            )) ?>

            - <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'classified', 'controller' => 'index', 'action' => 'delete', 'classified_id' => $item->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete'), array(
              'class' => 'smoothbox'
            )) ?>

            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>

  <?php elseif($this->search): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any classified listing that match your search criteria.');?>
      </span>
    </div>
  <?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any classified listings.');?>
        <?php if ($this->can_create): ?>
          <?php echo $this->translate('Get started by <a href=\'%1$s\' class="touchajax">posting</a> a new listing.', $this->url(array('action' => 'create'), 'classified_general'));?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?>
  </div>
</div>
