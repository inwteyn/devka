<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _touchActivityText.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>
<script type="text/javascript">
  function showCommentForm(form_id){
    $(document.body).getElements('form.activity-comment-form').setStyle('display', 'none');
    var form = $(form_id);
    var ta = form.getElement('textarea');
    document.getElementById(form_id).style.display = "";
    form.getElement('button').style.display = "block";
    window.scrollTo(0, ta.getPosition().y - (window.getHeight()/2));
    ta.focus();
  }
</script>

<?php if( empty($this->actions) ) { echo $this->translate("The action you are looking for does not exist."); return; } else { $actions = $this->actions; } ?>

<?php
  foreach( $actions as $action ): // (goes to the end of the file)
    try {
      if( !$action->getTypeInfo()->enabled ) continue;
      if( !$action->getSubject() || !$action->getSubject()->getIdentity() ) continue;
      if( !$action->getObject() || !$action->getObject()->getIdentity() ) continue;
      
      ob_start();
    ?>
  <?php if( !$this->noList ): ?><li id="activity-item-<?php echo $action->action_id ?>" class='touch-list-item'><?php endif; ?>
  	<?php $this->commentForm->setActionIdentity($action->action_id) ?>
    <script type="text/javascript">
      (function(){
        var action_id = '<?php echo $action->action_id ?>';
        en4.core.runonce.add(function(){
          Touch.bind($('activity-item-' + action_id));
          $('activity-comment-body-' + action_id).autogrow();
          en4.activity.attachComment($('activity-comment-form-' + action_id));
        });
      })();
    </script>
    <div class='feed_item_photo'>
		<?php echo $this->htmlLink($action->getSubject()->getHref(), $this->itemPhoto($action->getSubject(), 'thumb.icon', $action->getSubject()->getTitle()), array('class'=>'touchajax')) ?></div>


    <div class='feed_item_body'>
      
      <?php // Main Content ?>
      <span class="<?php echo ( empty($action->getTypeInfo()->is_generated) ? 'feed_item_posted' : 'feed_item_generated' ) ?>">
        <?php echo str_replace('feed_item_username', 'feed_item_username touchajax',  $action->getContent()); ?>
      </span>

      <?php // Attachments ?>
      <?php if( $action->getTypeInfo()->attachable && $action->attachment_count > 0 ): // Attachments ?>
        <div class='feed_item_attachments'>
          <?php if( $action->attachment_count > 0 && count($action->getAttachments()) > 0 ): ?>
            <?php if( count($action->getAttachments()) == 1 &&
                    null != ( $richContent = $this->touchRichContent( current($action->getAttachments())->item) ) ): ?>
              <?php echo $richContent; ?>
            <?php else: ?>
              <?php foreach( $action->getAttachments() as $attachment ): ?>
                <span class='feed_attachment_<?php echo $attachment->meta->type ?>'>
                <?php if( $attachment->meta->mode == 0 ): // Silence ?>
                <?php elseif( $attachment->meta->mode == 1 ): // Thumb/text/title type actions ?>
                  <div>
                    <?php if( $attachment->item->getPhotoUrl() ): ?>
                      <?php 
                        if ($attachment->item->getType() == "core_link")
                        {
                          $attribs = Array('target'=>'_blank', 'class'=>'touchajax');
                        }
                        else
                        {
                          $attribs = Array('class'=>'touchajax');
                        } 
                      ?>
                      <?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle()), $attribs) ?>
                    <?php endif; ?>
                    <div>
                      <div class='feed_item_link_title'>
                        <?php
                          if ($attachment->item->getType() == "core_link")
                          {
                            $attribs = Array('class'=>'touchajax');
                          }
                          else
                          {
                            $attribs = Array('class'=>'touchajax');
                          }
                          echo $this->htmlLink($attachment->item->getHref(), $attachment->item->getTitle() ? $attachment->item->getTitle() : '', $attribs);
                        ?>
                      </div>
                      <div class='feed_item_link_desc'>
                        <?php echo $this->viewMore($attachment->item->getDescription()) ?>
                      </div>
                    </div>
                  </div>
                <?php elseif( $attachment->meta->mode == 2 ): // Thumb only type actions ?>
                  <div class="feed_attachment_photo">
                    <?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle()), array('class' => 'feed_item_thumb touchajax')) ?>
                  </div>
                <?php elseif( $attachment->meta->mode == 3 ): // Description only type actions ?>
                  <?php echo $this->viewMore($attachment->item->getDescription()); ?>
                <?php elseif( $attachment->meta->mode == 4 ): // Multi collectible thingy (@todo) ?>
                <?php endif; ?>
                </span>
              <?php endforeach; ?>
              <?php endif; ?>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php // Icon, time since, action links ?>
      <?php
        $icon_type = 'activity_icon_'.$action->type;
        list($attachment) = $action->getAttachments();
        if( is_object($attachment) && $action->attachment_count > 0 && $attachment->item ):
          $icon_type .= ' item_icon_'.$attachment->item->getType() . ' ';
        endif;
      ?>
      <div class='feed_item_date feed_item_icon <?php echo $icon_type ?> item_date'>
        <ul>
        <?php echo $this->timestamp($action->getTimeValue()) ?>
          <li>
        <?php if( $action->getTypeInfo()->commentable && $this->viewer()->getIdentity() && Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment') ): ?>
          <?php if( $action->likes()->isLike($this->viewer()) ): ?>
              <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Unlike'), array('onclick'=>'javascript:en4.activity.unlike('.$action->action_id.');', 'class'=>'action-unlike feed-option')) ?>
          <?php else: ?>
              <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Like'), array('onclick'=>'javascript:en4.activity.like('.$action->action_id.');', 'class'=>'action-like feed-option')) ?>
          <?php endif; ?>

          <?php if( isset($this->commentForm) && Engine_Api::_()->getApi('settings', 'core')->core_spam_comment ): // Comments - likes ?>
            <?php echo $this->htmlLink(array('route'=>'default','module'=>'activity','controller'=>'index','action'=>'viewcomment','action_id'=>$action->getIdentity(),'format'=>'smoothbox'), $this->translate('Comment'), array(
                'class'=>'action-comment feed-option',
            )) ?>
          <?php elseif( isset($this->commentForm) ): ?>
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Comment'), array('onclick'=>'showCommentForm("'.$this->commentForm->getAttrib('id').'")', 'class'=>'action-comment feed-option')) ?>
          <?php endif; ?>

          <?php if( $this->activity_moderate || (
              $this->viewer()->getIdentity() && $this->allow_delete && (
                ('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
                ('user' == $action->object_type && $this->viewer()->getIdentity()  == $action->object_id)               )
            ) ): ?>
            <?php echo $this->htmlLink(array(
              'route' => 'default',
              'module' => 'activity',
              'controller' => 'index',
              'action' => 'delete',
              'action_id' => $action->action_id
              ), $this->translate('Delete'), array('class' => 'touchconfirm feed-option action-delete')) ?>
          <?php endif; ?>

        <?php endif; ?>

        <?php // Share ?>
        <?php if( $action->getTypeInfo()->shareable && $this->viewer()->getIdentity() ): ?>
          <?php if( $action->getTypeInfo()->shareable == 1 && $action->attachment_count == 1 && ($attachment = $action->getFirstAttachment()) ): ?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $attachment->item->getType(), 'id' => $attachment->item->getIdentity()), $this->translate('Share'), array('class' => 'smoothbox feed-option action-share', 'title' => 'Share')) ?>
          <?php elseif( $action->getTypeInfo()->shareable == 2 ): ?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $subject->getType(), 'id' => $subject->getIdentity()), $this->translate('Share'), array('class' => 'smoothbox feed-option action-share', 'title' => 'Share')) ?>
            <?php elseif( $action->getTypeInfo()->shareable == 3 ): ?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $object->getType(), 'id' => $object->getIdentity()), $this->translate('Share'), array('class' => 'smoothbox feed-option action-share', 'title' => 'Share')) ?>
            <?php elseif( $action->getTypeInfo()->shareable == 4 ): ?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $action->getType(), 'id' => $action->getIdentity()), $this->translate('Share'), array('class' => 'smoothbox feed-option action-share', 'title' => 'Share')) ?>
          <?php endif; ?>
        <?php endif; ?>
          </li>
      </ul>
      </div>

      <?php if( $action->getTypeInfo()->commentable ): // Comments - likes ?>
        <div class='comments touch-comments'>
          <ul>
            <?php if( $action->likes()->getLikeCount() > 0 && (count($action->likes()->getAllLikesUsers())>0) ): ?>
              <li>
                <div></div>
                <div class="comments_likes">
                  <?php if( $action->likes()->getLikeCount() <= 3 || $this->viewAllLikes ): ?>
                    <?php echo $this->translate(array('%s likes this.', '%s like this.', $action->likes()->getLikeCount()), $this->fluentList($action->likes()->getAllLikesUsers()) )?>

                  <?php else: ?>
                    <?php echo $this->htmlLink(array('route' => 'user_profile', 'id' => $action->getSubject()->username, 'action_id' => $action->action_id, 'show_likes' => true),
                                              $this->translate(array('%s person likes this', '%s people like this', $action->likes()->getLikeCount()), $this->locale()->toNumber($action->likes()->getLikeCount()) )
                    ) ?>
                  <?php endif; ?>
                </div>
              </li>
            <?php endif; ?>
            <?php if( $action->comments()->getCommentCount() > 0 ): ?>
              <?php if( $action->comments()->getCommentCount() > 2 && !$this->viewAllComments): ?>
                <li>
                  <div></div>
                  <div class="comments_viewall">
                    <?php if( $action->comments()->getCommentCount() > 2): ?>
                      <?php echo $this->htmlLink(array('route'=>'user_profile','id'=>$action->getSubject()->username,'action_id'=>$action->action_id, 'show_comments'=>true),
                                                 $this->translate(array('View all %s comment', 'View all %s comments', $action->comments()->getCommentCount()),
                                                                  $this->locale()->toNumber($action->comments()->getCommentCount()))) ?>
                    <?php else: ?>
                      <?php echo $this->htmlLink('javascript:void(0);',
                                                 $this->translate(array('View all %s comment', 'View all %s comments', $action->comments()->getCommentCount()),
                                                                  $this->locale()->toNumber($action->comments()->getCommentCount())),
                                                 array('onclick'=>'en4.activity.viewComments('.$action->action_id.');')) ?>
                    <?php endif; ?>
                  </div>
                </li>
              <?php endif; ?>
              <?php foreach( Engine_Api::_()->touch()->touchGetComments($action, $this->viewAllComments) as $comment ): ?>
                <li id="comment-<?php echo $comment->comment_id ?>">
                   <div class="comments_info">
                     <span class='comments_author'>
                       <?php echo $this->htmlLink($this->item($comment->poster_type, $comment->poster_id)->getHref(), $this->item($comment->poster_type, $comment->poster_id)->getTitle(), array('class'=>'touchajax')); ?>
                     </span>
                     <?php echo $this->viewMore($comment->body) ?>
                     <div class="comments_date">
                       <?php echo $this->timestamp($comment->creation_date); ?>
                       <?php if ( $this->viewer()->getIdentity() &&
                                 (('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
                                  ($this->viewer()->getIdentity() == $comment->poster_id) ) ): ?>
                       <?php echo $this->htmlLink(array(
                            'route'=>'default',
                            'module'    => 'activity',
                            'controller'=> 'index',
                            'action'    => 'delete',
                            'action_id' => $action->action_id,
                            'comment_id'=> $comment->comment_id,
                            ), $this->translate('Delete'), array('class'=>'touchconfirm feed-option')) ?>
                       <?php endif; ?>
                     </div>
                   </div>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>

          <?php if( isset($this->commentForm) ) echo $this->commentForm->render() ?>
        </div>
      <?php endif; ?>

    </div>
  <?php if( !$this->noList ): ?></li><?php endif; ?>
<?php
      ob_end_flush();
    } catch (Exception $e) {
      ob_end_clean();
      if( APPLICATION_ENV === 'development' ) {
        echo $e->__toString();
      }
    };
  endforeach;
?>
