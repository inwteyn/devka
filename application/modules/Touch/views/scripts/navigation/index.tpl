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

?><?php $active = $this->container->findOneBy('active', true);?>

<div class="touch-navigation">
	<div class="navigation-header">
		<div id="navigation-selector">
			<?php if ($active):?>
        <?php echo $this->touchSubstr($this->translate($active->getLabel())); ?>
      <?php else :?>
        <?php
          $items = $this->container->getPages();
          $first = array_shift($items);
      if(isset($this->container->title))
        $first = $this->container->title;
        ?>
        <?php echo $this->touchSubstr($this->translate($first->getLabel())); ?>
      <?php endif;?>
		</div>

		<div class="navigation-body">
			<div id="navigation-items">
				<?php	foreach ($this->container as $item): ?>
					<div class="item<?php if ($item->isActive()): ?> active <?php endif; ?>">
						<a href="<?php echo $item->getHref(); ?>" onclick="Touch.navigation.request($(this)); return false;">
							<?php echo $this->touchSubstr($this->translate($item->getLabel())); ?>
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