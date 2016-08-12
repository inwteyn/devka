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

?>
<div class="dashboard">
	<ul class="dashboard-items">
		<?php foreach( $this->navigation as $item ): ?>
      <?php
      $class = $item->getClass();
      $style = '';
      $target =null;
      $target = $item->getTarget();
        if( strstr($class, 'custom') )
        {
          $properties = $item->getCustomProperties();
          $icon = $properties['icon'];
           $style = ' style="background-image:url('.$icon.');background-position:center top;background-repeat:no-repeat;" ';
        }
      ?>
			<li class="<?php echo $item->getClass(); ?>" <?php echo $style ?> >
				<a
          <?php if($target): ?>
            target="<?php echo $target; ?>"
          <?php endif; ?>
            href="<?php echo method_exists($item, 'getHref')? $item->getHref(): "#" ?>"
          <?php if(!strstr($class, 'custom') && !strstr($class, 'core_dashboard_home')){ ?>
            class="touchajax"
          <?php }?>
        >
          <?php  echo $this->translate($item->getLabel()); ?>
        </a>
			</li>
		<?php endforeach; ?>
	</ul>
</div>