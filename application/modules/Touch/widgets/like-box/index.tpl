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

?><script type="text/javascript">
(function(){
      var likes_count = <?php echo sprintf('%d', $this->total) ?>;
      var subject_guid = '<?php echo $this->subjectGuid ?>';
      var this_total = '<?php echo $this->this_total ?>';

      var likeViewMore = function(subject_guid) {
        if( en4.core.request.isRequestActive() ) return;

        var url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'content_id' => $this->identity, 'page' => $this->next_page), 'default', true) ?>';
        $('like_viewmore').style.display = 'none';
        $('like_loading').style.display = '';

        var request = new Request.HTML({
          url : url,
          data : {
            format : 'html',
            'nolayout' : true,
            'subject' : subject_guid
          },
          evalScripts : true,
          onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
            $('like_loading').style.display = 'none';
            Elements.from(responseHTML)[0].getElements('ul li').inject($('likes'));
            en4.core.runonce.trigger();
						Touch.bind('likes');
          }
        });
       request.send();
      }

      en4.core.runonce.add(function(){
        if(likes_count > this_total) {
          $('like_viewmore').style.display = '';
          $('like_loading').style.display = 'none';
					$('like_viewmore_link').removeProperty('onclick');
          $('like_viewmore_link').removeEvents('click').addEvent('click', function(event){
            event.stop();
            likeViewMore(subject_guid);
          });
        } else {
          $('like_viewmore').style.display = 'none';
          $('like_loading').style.display = 'none';
        }
      });
    })();
</script>

<ul class='items' id="likes">

  <?php foreach ($this->likes as $like): ?>
    <li>
			<div class="item_photo">
      	<?php echo $this->htmlLink($like->getHref(), $this->itemPhoto($like, 'thumb.icon'), array('class' => 'profile_friends_icon touchajax')) ?>
			</div>

      <div class='item_body'>
        <div class='item_title'>
          <?php echo $this->htmlLink($like->getHref(), $like->getTitle(), array('class' => 'touchajax')); ?>
        </div>

				<div class='item_options'>
        	<?php echo $this->touchUserFriendship($like); ?>
      	</div>

      </div>

    </li>

  <?php endforeach;?>
</ul>

<div class="like_viewmore" id="like_viewmore">
  <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
    'id' => 'like_viewmore_link',
    'class' => 'buttonlink icon_viewmore',
  )) ?>
</div>

<div class="like_viewmore" id="like_loading" style="display: none;">
  <img src='application/modules/Core/externals/images/loading.gif' style='float:left;margin-right: 5px;' />
  <?php echo $this->translate("Loading ...") ?>
</div>