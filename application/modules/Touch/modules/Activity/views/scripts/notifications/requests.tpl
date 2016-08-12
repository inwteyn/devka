<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: requests.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>

<?php
	// Render the menu & paginator
	echo $this->navigationPaginator($this->navigation, $this->requests);
?>

<div id="navigation_content">
	<ul class="requests">
		<?php if( $this->requests->getTotalItemCount() > 0 ): ?>
			<?php foreach( $this->requests as $notification ): ?>
				<?php
					$parts = explode('.', $notification->getTypeInfo()->handler);
					echo $this->touchAction($parts[2], $parts[1], $parts[0], array('notification' => $notification))	;
				?>
			<?php endforeach; ?>
		<?php else: ?>
			<div class="tip">
				<span>
					<?php echo $this->translate("You have no requests.") ?>
				</span>
			</div>
		<?php endif; ?>
	</ul>
</div>