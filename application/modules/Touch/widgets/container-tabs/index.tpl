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
<div class="touch-navigation">
	<div class="navigation-header">
		<?php	foreach ($this->tabs as $key => $tab): ?>
				<?php if( $this->activeTab == $tab['id'] ):?>
				<div id="navigation-selector">
					<?php echo $this->translate($tab['title']); ?><?php if( !empty($tab['childCount']) ): ?><span>(<?php echo $tab['childCount'] ?>)</span><?php endif; ?>
				</div>
				<?php endif; ?>
		<?php endforeach; ?>
		
		<div class="navigation-body">
			<div id="navigation-items">
				<?php	foreach ($this->tabs as $key => $tab): ?>
					<div class="item<?php if( $this->activeTab == $tab['id'] ){ $active_tab_name = $tab['title']?> active <?php }; ?>">
						<a href="<?php echo $this->url() . '?tab=' .$tab['id']. '&from_tl=' .($this->is_tl?1:0) ?>" onclick="Touch.navigation.request($(this)); return false;">
							<?php echo $this->translate($tab['title']) ?> <?php if( !empty($tab['childCount']) ): ?><span>(<?php echo $tab['childCount'] ?>)</span><?php endif; ?>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>

<div style="height:10px"></div>
<div id="navigation_loading" style="display: none;">
	<a class="loader"><?php echo $this->translate("Loading"); ?>...</a>
</div>
<div id="navigation_content">
	<?php echo $this->childrenContent ?>
</div>