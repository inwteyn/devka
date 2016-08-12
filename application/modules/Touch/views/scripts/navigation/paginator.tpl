<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: paginator.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?><?php $active = $this->navigation->findOneBy('active', true);
?>

<div class="touch-navigation">
	<div class="navigation-header navigation-paginator">
		<span class="touch-navigation-paginator">
<?php
if($this->layout()->orientation == 'right-to-left'){
  ?>
			<?php if (isset($this->paginator->getPages()->next)): ?>
				<a class="paginator-navigation" href="<?php echo $this->url(array('page' => $this->paginator->getPages()->next)); ?>" onclick="Touch.navigation.request($(this)); return false;">
					<img src="application/modules/Touch/themes/<?php echo $this->touchActiveTheme(); ?>/images/prev.png" alt="<?php echo $this->translate('Prev') ?>" />
				</a>
			<?php else: ?>
				<span class="paginator-navigation"><img src="application/modules/Touch/themes/<?php echo $this->touchActiveTheme(); ?>/images/prev_disabled.png" alt="<?php echo $this->translate('Next') ?>"/></span>
			<?php endif; ?>

    <?php if (isset($this->paginator->getPages()->previous)): ?>
        <a	class="paginator-navigation" href="<?php echo $this->url(array('page' => $this->paginator->getPages()->previous)); ?>" onclick="Touch.navigation.request($(this)); return false;">
            <img src="application/modules/Touch/themes/<?php echo $this->touchActiveTheme(); ?>/images/nex.png" alt="<?php echo $this->translate('Prev') ?>" />
        </a>
      <?php else: ?>
        <span class="paginator-navigation"><img src="application/modules/Touch/themes/<?php echo $this->touchActiveTheme(); ?>/images/next_disabled.png" alt="<?php echo $this->translate('Prev') ?>" /></span>
      <?php endif; ?>

  <?php } else { ?>
			<?php if (isset($this->paginator->getPages()->previous)): ?>
				<a	class="paginator-navigation" href="<?php echo $this->url(array('page' => $this->paginator->getPages()->previous)); ?>" onclick="Touch.navigation.request($(this)); return false;">
						<img src="application/modules/Touch/themes/<?php echo $this->touchActiveTheme(); ?>/images/prev.png" alt="<?php echo $this->translate('Prev') ?>" />
				</a>
			<?php else: ?>
				<span class="paginator-navigation"><img src="application/modules/Touch/themes/<?php echo $this->touchActiveTheme(); ?>/images/prev_disabled.png" alt="<?php echo $this->translate('Prev') ?>" /></span>
			<?php endif; ?>

			<?php if (isset($this->paginator->getPages()->next)): ?>
				<a class="paginator-navigation" href="<?php echo $this->url(array('page' => $this->paginator->getPages()->next)); ?>" onclick="Touch.navigation.request($(this)); return false;">
					<img src="application/modules/Touch/themes/<?php echo $this->touchActiveTheme(); ?>/images/next.png" alt="<?php echo $this->translate('Prev') ?>" />
				</a>
			<?php else: ?>
				<span class="paginator-navigation"><img src="application/modules/Touch/themes/<?php echo $this->touchActiveTheme(); ?>/images/next_disabled.png" alt="<?php echo $this->translate('Next') ?>"/></span>
			<?php endif; ?>
  <?php } ?>
		</span>

		<div id="navigation-selector">
			<?php echo $active->getLabel() == 'My Notifications' ? $this->translate($active->getLabel()) : $active->getLabel(); ?>
		</div>

		<div class="navigation-body">
			<div id="navigation-items">
				<?php	foreach ($this->navigation as $item): ?>
					<div class="item<?php if ($item->isActive()): ?> active <?php endif; ?>">
						<a href="<?php echo $item->getHref(); ?>" <?php if ($item->full_redirect == 1):?>class="touchajax"<?php else:?>onclick="Touch.navigation.request($(this)); return false;"<?php endif;?>>
							<?php echo $this->translate($item->getLabel()); ?>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>

<div style="height:10px"></div>

<div id="navigation_loading" style="display:none; text-align: center; vertical-align: middle;">
	<a class="loader"><?php echo $this->translate("Loading ..."); ?></a>
</div>