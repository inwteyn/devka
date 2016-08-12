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
	Photobox.isOpen = true;
	en4.core.runonce.add(function(){
		var a_els = $('activity-feed').getElements('a');
		a_els.each(function(el){
			if (!el.hasClass('feed-option') && !el.hasClass('touchajax') && el.get('href') != 'javascript:void(0);'){
				el.addClass('touchajax');
			}
		});
	});
})();
</script>

<?php if( (!empty($this->feedOnly) || !$this->endOfFeed ) && (empty($this->getUpdate) && empty($this->checkUpdate)) ):?>
  <script type="text/javascript">
    (function(){
      var activity_count = <?php echo sprintf('%d', $this->activityCount) ?>;
      var next_id = <?php echo sprintf('%d', $this->nextid) ?>;
      var subject_guid = '<?php echo $this->subjectGuid ?>';
      var endOfFeed = <?php echo ( $this->endOfFeed ? 'true' : 'false' ) ?>;

      var activityViewMore = function(next_id, subject_guid) {
        if( en4.core.request.isRequestActive() ) return;

        var url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'content_id' => $this->identity), 'default', true) ?>';
        $('feed_viewmore').style.display = 'none';
        $('feed_loading').style.display = '';

        var request = new Request.HTML({
          url : url,
          data : {
            format : 'html',
            'maxid' : next_id,
            'feedOnly' : true,
            'nolayout' : true,
            'subject' : subject_guid
          },
          evalScripts : true,
          onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
            Elements.from(responseHTML).inject($('activity-feed'));
            en4.core.runonce.trigger();
						Touch.bind('activity-feed');
          }
        });
       request.send();
      }

      en4.core.runonce.add(function(){
        if( next_id > 0 && !endOfFeed ) {
          $('feed_viewmore').style.display = '';
          $('feed_loading').style.display = 'none';
					$('feed_viewmore_link').removeProperty('onclick');
          $('feed_viewmore_link').removeEvents('click').addEvent('click', function(event){
            event.stop();
            activityViewMore(next_id, subject_guid);
          });
        } else {
          $('feed_viewmore').style.display = 'none';
          $('feed_loading').style.display = 'none';
        }
      });
    })();

  </script>
<?php endif; ?>

<?php if( !empty($this->feedOnly) && empty($this->checkUpdate)): // Simple feed only for AJAX
  echo $this->touchActivityLoop($this->activity, array(
    'action_id' => $this->action_id,
    'viewAllComments' => $this->viewAllComments,
    'viewAllLikes' => $this->viewAllLikes,
  ));
  return; // Do no render the rest of the script in this mode
endif; ?>

<?php if( !empty($this->checkUpdate) ): // if this is for the live update
    if ($this->activityCount)
  echo "<script type='text/javascript'>
          document.title = '($this->activityCount) ' + activityUpdateHandler.title;
        </script>

        <div class='tip'>
          <span>
            <a href='javascript:void(0);' onclick='javascript:activityUpdateHandler.getFeedUpdate(".$this->firstid.");$(\"feed-update\").empty();'>
              {$this->translate(array(
                  '%d new update is available - click this to show it.',
                  '%d new updates are available - click this to show them.',
                  $this->activityCount),
                $this->activityCount)}
            </a>
          </span>
        </div>";
  return; // Do no render the rest of the script in this mode
endif; ?>

<?php if( !empty($this->getUpdate) ): // if this is for the get live update ?>
   <script type="text/javascript">
     activityUpdateHandler.options.last_id = <?php echo sprintf('%d', $this->firstid) ?>;
     en4.core.runonce.add(function(){
     })
   </script>
<?php endif; ?>

<?php if( $this->enableComposer ): ?>
	<ul class='feed'><li>
    <?php if($this->viewer()->getIdentity()){ ?>
      <div class="feed_item_photo">
        <?php echo $this->htmlLink($this->viewer()->getHref(), $this->itemPhoto($this->viewer(), 'thumb.icon'), array('class' => 'touchajax')) ?>
      </div>
    <?php } ?>
		<div class="feed_item_body">
			<div class="activity-post-container">

				<form method="post"
							action="<?php echo $this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'post'), 'default', true) ?>"
							class="global_form_box"
							onsubmit="Touch.feed.post($(this)); return false;">
          <div class="textareadiv">
					<textarea id="body" name="body" class="feed-default-value"
              onfocus="Touch.feed.focus($(this), 'feed-default-value'); $(this).autogrow();"
              onblur="Touch.feed.blur($(this), 'feed-default-value', '<?php echo addslashes($this->translate("TOUCH_What's on your mind?")); ?>');"><?php echo $this->translate("TOUCH_What's on your mind?"); ?>
            </textarea>
          </div>
					<input type="hidden" name="return_url" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />

					<?php if( $this->viewer() && $this->subject() && !$this->viewer()->isSelf($this->subject())): ?>
						<input type="hidden" name="subject" value="<?php echo $this->subject()->getGuid() ?>" />
					<?php endif; ?>

					<div class="feed-submit feed-submit-hidden">
						<button id="touch-compose-submit" type="submit" style="width: 100%"><span><?php echo $this->translate("Share") ?></span></button>
						<span id='feed-post-loading'>
							<?php echo $this->translate('TOUCH_Posting') . '...' ?>
						</span>
					</div>
				</form>

			</div>
		</div>
	</li></ul>
<?php endif; ?>

<?php if( $this->post_failed == 1 ): ?>
  <div class="tip">
    <span>
      <?php $url = $this->url(array('module' => 'user', 'controller' => 'settings', 'action' => 'privacy'), 'default', true) ?>
      <?php echo $this->translate('The post was not added to the feed. Please check your %1$sprivacy settings%2$s.', '<a href="'.$url.'">', '</a>') ?>
    </span>
  </div>
<?php endif; ?>

<?php // If requesting a single action and it doesn't exist, show error ?>
<?php if( !$this->activity ): ?>
	<?php if( $this->action_id ): ?>
		<h4>&raquo; <?php echo $this->translate("Activity Item Not Found") ?></h4>
		<p>
			<?php echo $this->translate("The page you have attempted to access could not be found.") ?>
		</p>
	<?php return; else: ?>
		<div class="tip">
			<span>
				<?php echo $this->translate("Nothing has been posted here yet - be the first!") ?>
			</span>
		</div>
	<?php return; endif; ?>
<?php endif; ?>

<div id="feed-update"></div>

<ul id="activity-feed" class='feed'>
	<?php echo $this->touchActivityLoop($this->activity, array(
		'action_id' => $this->action_id,
		'viewAllComments' => $this->viewAllComments,
		'viewAllLikes' => $this->viewAllLikes,
	))?>
</ul>

<?php
	$url = $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'content_id' => $this->identity), 'default', true);
?>

<div class="feed_viewmore" id="feed_viewmore" style="<?php if ($this->nextid == 0 || $this->endOfFeed): ?> display: none; <?php endif; ?>">
  <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
    'id' => 'feed_viewmore_link',
    'class' => 'buttonlink icon_viewmore',
		'onClick' => "Touch.feed.viewmore(" . $this->nextid . ", '" . $this->subjectGuid . "', '" . $url . "'" . ");"
  )) ?>
</div>

<div class="feed_viewmore" id="feed_loading" style="display: none;">
  <a class="loader"> <?php echo $this->translate("Loading ...") ?></a>
</div>