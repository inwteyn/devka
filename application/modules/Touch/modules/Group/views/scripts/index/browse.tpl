<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: browse.tpl 2011-04-26 11:18:13 mirlan $
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
	<div	class="search">
		<?php echo $this->paginationControl(
				$this->paginator,
				null,
				array('pagination/filter.tpl', 'touch'),
				array(
					'search'=>$this->formFilter->getElement('search')->getValue(),
					'filter_default_value'=>$this->translate('TOUCH_Search Groups'),
					'filterUrl'=>$this->url(array(), 'group_general', true),
				)
		); ?>
	</div>

	<div id="filter_block">

   <?php if( count($this->paginator) > 0 ): ?>

    <ul class='items'>
      <?php foreach( $this->paginator as $group ): ?>
        <li>
          <div class="item_photo">
            <?php echo $this->htmlLink($group->getHref(), $this->itemPhoto($group, 'thumb.normal'), array('class' => 'touchajax')) ?>
          </div>
          <div class="item_body">
            <div class="item_title">
              <?php echo $this->htmlLink($group->getHref(), $group->getTitle(), array('class' => 'touchajax')) ?>
            </div>
            <div class="item_date">
              <?php echo $this->translate(array('%s member', '%s members', $group->membership()->getMemberCount()),$this->locale()->toNumber($group->membership()->getMemberCount())) ?>
              <?php echo $this->translate('led by');?> <?php echo $this->htmlLink($group->getOwner()->getHref(), $group->getOwner()->getTitle(), array('class' => 'touchajax')) ?>
            </div>
          </div>
          <?php echo $this->touchGroupRate('group', $group->getIdentity())?>
        </li>
      <?php endforeach; ?>
    </ul>

    <?php else: ?>
      <div class="tip">
        <span>
        <?php echo $this->translate('There are no groups yet.') ?>
        <?php if( $this->canCreate): ?>
          <?php echo $this->translate('Why don\'t you %1$screate one%2$s?',
            '<a href="'.$this->url(array('action' => 'create'), 'group_general').'" class="touchajax">', '</a>') ?>
        <?php endif; ?>
        </span>
      </div>
    <?php endif; ?>

	</div>
</div>

