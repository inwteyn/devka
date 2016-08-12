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
<script type="text/javascript">
	(function(){
	en4.core.runonce.add(function(){

		$('notification_menu').addEvent('click', function(event){

			var notification_li = event.target;
			if (notification_li.hasClass('notification_item_general')) notification_li = notification_li.getParent('li');

			var forward_link;
			if(notification_li.get('href')){
				forward_link = notification_li.get('href');
			}
			else{
				forward_link = $(notification_li).getElement('a:last-child').get('href');
			}

			if(notification_li.hasClass('unread')){
				notification_li.removeClass('unread');
				en4.core.request.send(new Request.JSON({
					url : en4.core.baseUrl + 'activity/notifications/markread',
					data : {
						format     : 'json',
						'actionid' : notification_li.get('value')
					},
					onSuccess : function(response){
						Touch.goto(forward_link);
            var notifcount = response.notificationCount;
            if(notifcount>0){
              $('updates_toggle').set('text', response.notificationCount);
            } else{
              $('updates_toggle').set('text', response.notificationCount);
              $('updates_toggle').removeClass('notifications_active').addClass('notifications');
            }
					}
				}));
			}
			else Touch.goto(forward_link);
		});
	});
})();
</script>

<?php
	// Render the menu & paginator
	echo $this->navigationPaginator($this->navigation, $this->notifications);
?>

<div id="navigation_content">
		<?php if( $this->notifications->getTotalItemCount() > 0 ): ?>
			<ul class='items notification_items' id="notification_menu">
			<?php foreach( $this->notifications as $notification ):
				ob_start();
				try { ?>

					<li class="<?php if( !$notification->read ): ?>unread<?php else: ?>read<?php endif; ?>" value="<?php echo $notification->getIdentity();?>">
						<div class="notification_item_general notification_type_<?php echo $notification->type ?> buttonlink">
							<?php echo $notification->__toString() ?>
						</div>
					</li>
				<?php
				} catch( Exception $e ) {
					ob_end_clean();
					if( APPLICATION_ENV === 'development' ) {
						echo $e->__toString();
					}
					continue;
				}
				ob_end_flush();
				endforeach;
			?>
		</ul>
		<?php else: ?>
			<div class = 'tip' style="text-align:center">
        <span>
				  <?php echo $this->translate("You have no notifications.") ?>
        </span>
			</div>
		<?php endif; ?>
</div>