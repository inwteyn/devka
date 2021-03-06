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
<div id='touch_mini_search_button'>
	<ul>
    <?php if( $this->viewer->getIdentity() && $this->notificationCount > 0) :?>
    <li id='touch_menu_mini_menu_update'>
      <span style="display: inline-block;" class="updates_pulldown">
				<?php
				echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'notifications'),
               $this->locale()->toNumber($this->notificationCount),
               array('id' => 'updates_toggle', 'class' => 'new_updates')) ?>
      </span>
    </li>
    <?php endif; ?>

    <?php if($this->search_check):?>
      <li id="global_search_form_container">
				<a id="global_search_link" href="<?php echo $this->url(array('controller' => 'search'), 'default', true) ?>" method="get">
					<img id='global_search_img' src="application/modules/Touch/externals/images/search.png" border="0" alt="<?php echo $this->translate('search'); ?>" width="14"/>
				</a>
  		</li>
    <?php endif;?>
	</ul>
</div>
<div class="clr"></div>