<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     Bolot
 */

?>

<?php
$editable = Engine_Api::_()->getApi('settings', 'core')->getSetting('hecomment.edit.enabled', true) ? true : false;
$replyhide = Engine_Api::_()->getApi('settings', 'core')->getSetting('hecomment.hide.reply.enabled', true) ? true : false;
?>


<?php if ($this->action->getTypeInfo()->commentable): // Comments - likes ?>

    <?php
    $moduleTable = Engine_Api::_()->getDbTable('modules', 'core');
    $comments = null;
    $paginator = null;
    $comments = Engine_Api::_()->wall()->getCommentReply($this->action, $this->comment);

    $canComment = ($this->action->getTypeInfo()->commentable &&
        $this->viewer()->getIdentity() &&
        Engine_Api::_()->authorization()->isAllowed($this->action->getObject(), null, 'comment') &&
        !empty($this->commentForm));

    ?>

    <?php if ($this->action->likes()->getLikeCount() > 0 && (count($this->action->likes()->getAllLikesUsers()) > 0)): ?>

        <?php
        $like_users = array();

        foreach ($this->action->likes()->getAllLikesUsers() as $user) {
            if (Engine_Api::_()->wall()->isOwnerTeamMember($this->action->getObject(), $user)) {
                $page = Engine_Api::_()->wall()->getSubjectPage($this->action->getObject());
                $like_users[] = $page;
            } else {
                $like_users[] = $user;
            }
        }

        ?>
    <?php endif; ?>

    <?php if ($comments): ?>

        <?php $count_comments = count($comments); ?>

        <?php if ($replyhide): ?>
            <a href="javascript:void(0)" id="show_reply_comments_button-<?php echo $this->comment->comment_id; ?>"
               onclick="Wall.showReplyComments(<?php echo $this->comment->comment_id; ?>, this);"
               class="reply_comments_show_button hei-reply">
                <?php echo $this->translate(array('WALL_View %s Reply', 'WALL_View %s Replies', $count_comments), $count_comments) ?>
            </a>
        <?php endif; ?>
        <?php foreach ($comments as $comment): ?>
            <li rev="item-<?php echo $comment->comment_id ?>"
                class="wall-comment-item reply_comment_id-<?php echo $this->comment->comment_id; ?>"
                id="comment-<?php echo $comment->comment_id ?>" style="padding-left: 50px; <?php if ($replyhide) echo 'display: none;' ?>" >
                <div class="comments_author_photo">


                    <?php echo $this->htmlLink($this->item('user', $comment->poster_id)->getHref(),
                        $this->itemPhoto($this->item('user', $comment->poster_id), 'thumb.icon'),
                        array('class' => 'wall_liketips', 'rev' => $this->item('user', $comment->poster_id)->getGuid())) ?>

                </div>
                <div class="comments_info">
               <span class='comments_author'>



                   <?php echo $this->htmlLink($this->item('user', $comment->poster_id)->getHref(), $this->item('user', $comment->poster_id)->getTitle(), array('class' => 'wall_liketips', 'rev' => $this->item('user', $comment->poster_id)->getGuid())); ?>


               </span>
          <span class="comment_body_<?php echo $comment->comment_id ?>">
          <?php

          $body_comment = $comment->body;
          if ($moduleTable->isModuleEnabled('hashtag')) {
              $url = $this->url(array('module' => 'hashtag', 'controller' => 'index', 'action' => 'search'), 'default', true);


              if (count($this->hashtags) > 0) {
                  foreach ($this->hashtags as $tag) {
                      if (trim($tag['hashtag']) == "") {
                          continue;
                      }
                      if ($tag['resource_id'] == $this->action->getIdentity()) {
                          if ($this->name == $tag['hashtag']) {
                              $body_comment = str_ireplace('#' . $tag['hashtag'], '<a href="javascript:void(0)" class="comment_hashtag" onClick="click_hashtags(\'' . $url . '\',\'' . $tag['hashtag'] . '\',\'' . $this->translate("WALL_RECENT") . '\',\'\',\'' . $this->action->action_id . '\');" title="' . $this->translate('TITLE_LINK_HASHTAG') . '" style="font-weight: bold;">' . '#' . $tag['hashtag'] . '</a>', $body_comment);
                          } else {
                              $body_comment = str_ireplace('#' . $tag['hashtag'], '<a href="javascript:void(0)" class="comment_hashtag" onClick="click_hashtags(\'' . $url . '\',\'' . $tag['hashtag'] . '\',\'' . $this->translate("WALL_RECENT") . '\',\'\',\'' . $this->action->action_id . '\');" title="' . $this->translate('TITLE_LINK_HASHTAG') . ' ">' . '#' . $tag['hashtag'] . '</a>', $body_comment);
                          }
                      }
                  }
              }
          }


          if (Engine_Api::_()->getApi('settings', 'core')->getSetting('wall.content.nl2br', false)):?>
              <?php echo $this->wallViewMore(nl2br($body_comment)) ?>
          <?php else : ?>
              <?php echo $this->wallViewMore($body_comment) ?>
          <?php endif; ?>
</span>

                    <div class="comments_date">
                        <?php echo $this->timestamp($comment->creation_date); ?>

                        <!----------------------------------------------->
                        <?php if ($canComment):
                            if ($comment instanceof Core_Model_Comment) {
                                $isLiked = $comment->likes()->isLike($this->viewer());
                            }
                            if ($comment instanceof Wall_Model_Comment) {
                                $isLiked = $comment->isLike($this->viewer());
                            }
                            ?>
                            -
                            <?php if (!$isLiked): ?>
                            <a href="javascript:void(0)" class="comment-like" rev="reply">
                                <?php echo $this->translate('like') ?>
                            </a>
                        <?php else: ?>
                            <a href="javascript:void(0)" class="comment-unlike" rev="reply">
                                <?php echo $this->translate('unlike') ?>
                            </a>
                        <?php endif ?>
                            <!---------------------------------------------------->

                        <?php endif ?>
                        - <a href="javascript:void(0)" class="comment-reply"
                             rev="<?php echo $comment->getIdentity() ?>">
                            <?php echo $this->translate('Reply') ?>
                        </a>
                        <?php if ($editable && $this->viewer()->getIdentity() && $this->viewer()->getIdentity() == $comment->poster_id): ?>
                            - <a href="javascript:void(0);"
                                 class="comment-edit"><?php echo $this->translate('Edit') ?></a>
                        <?php endif; ?>
                        <?php if ($this->viewer()->getIdentity() &&
                            (('user' == $this->action->subject_type && $this->viewer()->getIdentity() == $this->action->subject_id) ||
                                ($this->viewer()->getIdentity() == $comment->poster_id) ||
                                $this->activity_moderate)
                        ): ?>
                            - <a href="javascript:void(0);" class="comment-remove"
                                 rev="reply"><?php echo $this->translate('Delete') ?></a>
                        <?php endif; ?>

                        <?php if ($comment->likes()->getLikeCount() > 0): ?>
                            -
                            <a
                                href="<?php echo $this->url(array('controller' => 'items', 'fn' => 'like', 'm' => 'wall', 'subject' => $comment->getGuid()), 'wall_extended', true) ?>"
                                class="comments_comment_likes smoothbox">
                                <?php echo $this->translate(array('%s likes this', '%s like this', $comment->likes()->getLikeCount()), @$this->locale()->toNumber($comment->likes()->getLikeCount())) ?>
                            </a>
                        <?php endif ?>

                    </div>
                </div>
                <div class="comment-form-reply" id="comment-form-reply_<?php echo $comment->getIdentity() ?>"
                     style="display: none">
                    <?php
                    echo $this->commentFormReply
                        ->setActionIdentity($this->action->action_id, $this->comment->getIdentity(), $comment->poster_id, $comment->getIdentity())
                        ->setAttrib('style', 'display:block;')
                        //->setUploadPhotoButton($this->action->action_id)
                        ->render();

                    ?>
                    <div id="preview_comment_attach_wall">
                        <div class="comment_attach_loading_wall"
                             id="comment_attach_loading_wall<?php echo $this->action->action_id . '_' . $this->comment->getIdentity() . '_' . $comment->getIdentity(); ?>"></div>
                        <div class="comment_attach_preview_image_wall"
                             id="comment_attach_preview_image_wall<?php echo $this->action->action_id . '_' . $this->comment->getIdentity() . '_' . $comment->getIdentity(); ?>"></div>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>

        <?php if ($replyhide): ?>
            <a href="javascript:void(0)" id="hide_reply_comments_button-<?php echo $this->comment->comment_id; ?>"
               onclick="Wall.hideReplyComments(<?php echo $this->comment->comment_id; ?>, this);" style="display: none"
               class="reply_comments_hide_button hei-share">
                <?php echo $this->translate(array("WALL_Hide reply", "WALL_Hide replies", $count_comments), $count_comments); ?>
            </a>
        <?php endif; ?>

    <?php endif; ?>

<?php endif; ?>


