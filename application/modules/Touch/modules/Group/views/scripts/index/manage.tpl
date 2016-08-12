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

	<div	class="search">
		<?php echo $this->paginationControl(
				$this->paginator,
				null,
				array('pagination/filter.tpl', 'touch'),
				array(
					'search'=>$this->formFilter->getElement('search')->getValue(),
					'filter_default_value'=>$this->translate('TOUCH_Search Groups'),
					'filterUrl'=>$this->url(array('action' => 'manage'), 'group_general', true),
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

                - <?php echo $this->htmlLink(array('route' => 'group_specific', 'action' => 'edit', 'group_id' => $group->getIdentity()), $this->translate('Edit'), array('class' => 'touchajax')) ?>

                <?php if( $group->isOwner($this->viewer()) ): ?>
                <a href="<?php echo $this->url(array('module' => 'group', 'controller' => 'group', 'action' => 'delete', 'group_id' => $group->getIdentity()), 'default', true); ?>" class="smoothbox">
                  - <?php echo $this->translate('Delete')?>
                </a>
                <?php elseif( !$group->membership()->isMember($this->viewer(), null) ): ?>
                  - <?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'member', 'action' => 'join', 'group_id' => $group->getIdentity()), $this->translate('Join Group'), array(
                    'class' => 'smoothbox icon_group_join'
                  )) ?>
                <?php elseif( $group->membership()->isMember($this->viewer(), true) && !$group->isOwner($this->viewer()) ): ?>
                  - <?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'member', 'action' => 'leave', 'group_id' => $group->getIdentity()), $this->translate('Leave Group'), array(
                    'class' => 'smoothbox icon_group_leave'
                  )) ?>
                <?php endif; ?>

              </div>
            </div>
            <?php echo $this->touchGroupRate('group', $group->getIdentity())?>
          </li>
        <?php endforeach; ?>
      </ul>

    <?php else: ?>
      <div class="tip">
        <span>
        <?php echo $this->translate('You have not joined any groups yet.') ?>
        <?php if( $this->canCreate): ?>
          <?php echo $this->translate('Why don\'t you %1$screate one%2$s?',
            '<a href="'.$this->url(array('action' => 'create'), 'group_general').'" class="touchajax">', '</a>') ?>
        <?php endif; ?>
        </span>
      </div>
    <?php endif; ?>

	</div>
</div>




