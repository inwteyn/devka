<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _comments.tpl 20.05.15 11:49 bolot $
 * @author     Bolot
 */
$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
$editable = Engine_Api::_()->getApi('settings', 'core')->getSetting('hecomment.edit.enabled', true) ? true : false;
$replyhide = Engine_Api::_()->getApi('settings', 'core')->getSetting('hecomment.hide.reply.enabled', true) ? true : false;

$table = Engine_Api::_()->getDbTable('comments', 'core');
$select = $table->select()->where('resource_id=?', $this->comment->getIdentity())->where('resource_type=?', 'comment');
$comments = $table->fetchAll($select);
if (count($comments) > 0) {
    ?>

    <?php $count_comments = count($comments); ?>

    <?php if ($replyhide): ?>
        <a href="javascript:void(0)" id="show_reply_comments_button-<?php echo $this->comment->comment_id; ?>"
           onclick="Hecomment.showReplyComments(<?php echo $this->comment->comment_id; ?>, this);"
           class="hecomment_reply_comments_show_button hei-reply">
            <?php echo $this->translate(array('HECOMMENT_View %s Reply', 'HECOMMENT_View %s Replies', $count_comments), $count_comments) ?>
        </a>
    <?php endif; ?>

    <?php foreach ($comments as $comment) {

        $poster = $this->item($comment->poster_type, $comment->poster_id);
        $canDelete = ($this->canDelete || $poster->isSelf($this->viewer()));
        if ($comment->getIdentity()) {
            ?>
            <li id="comment-<?php echo $comment->comment_id ?>" <?php if ($replyhide) echo 'style="display: none "'?> 
                class="hecomment_reply_comment_id-<?php echo $this->comment->comment_id; ?> hecomment_comment_reply">

                <div class="comments_author_photo">
                    <?php echo $this->htmlLink($poster->getHref(),
                        $this->itemPhoto($poster, 'thumb.icon', $poster->getTitle())
                    ) ?>
                </div>

                <div class="comments_info">
                    <span class='comments_author'>
                      <?php echo $this->htmlLink($poster->getHref(), $poster->getTitle()); ?>
                    </span>
                    <span class="comments_body">
                      <?php
                      $comment_body = explode('</br>', $comment->body, 2);

                      echo $this->viewMore($comment_body[0]);
                      echo '</br>' . $comment_body[1];
                      ?>
                    </span>

                    <div class="comments_date">
                        <?php echo $this->timestamp($comment->creation_date); ?>
                        <?php if ($this->canComment):
                            $isLiked = $comment->likes()->isLike($this->viewer());
                            ?>
                            -
                            <?php if (!$isLiked): ?>
                            <a href="javascript:void(0)"
                               onclick="Hecomment.core.comments.like(<?php echo sprintf("'%s', %d, %d", $this->subject()->getType(), $this->subject()->getIdentity(), $comment->getIdentity()) ?>)">
                                <?php echo $this->translate('like') ?>
                            </a>
                        <?php else: ?>
                            <a href="javascript:void(0)"
                               onclick="Hecomment.core.comments.unlike(<?php echo sprintf("'%s', %d, %d", $this->subject()->getType(), $this->subject()->getIdentity(), $comment->getIdentity()) ?>)">
                                <?php echo $this->translate('unlike') ?>
                            </a>
                        <?php endif ?>
                        <?php endif ?>
                        <?php if ($comment->likes()->getLikeCount() > 0): ?>
                            -
                            <a href="javascript:void(0);" id="comments_comment_likes_<?php echo $comment->comment_id ?>"
                               class="comments_comment_likes" title="<?php echo $this->translate('Loading...') ?>">
                                <?php echo $this->translate(array('%s likes this', '%s like this', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount())) ?>
                            </a>
                        <?php endif ?>
                        <?php if ($this->canComment) : ?>
                            - <a href="javascript:void(0)"
                                 onclick="Hecomment.core.comments.show_hide_comment_form(<?php echo $comment->comment_id ?>)">
                                <?php echo $this->translate('Reply') ?>
                            </a>
                        <?php endif ?>
                        <?php if ($editable && $comment->poster_id == $viewer_id): ?>
                            -
                            <a href="javascript:void(0);"
                               onclick="Hecomment.core.comments.editComment('<?php echo $comment->comment_id ?>')">
                                <?php echo $this->translate('Edit') ?>
                            </a>
                        <?php endif; ?>

                        <?php if ($canDelete): ?>
                            -
                            <a href="javascript:void(0);"
                               onclick="Hecomment.core.comments.deleteComment('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>', '<?php echo $comment->comment_id ?>')">
                                <?php echo $this->translate('Delete') ?>
                            </a>
                        <?php endif; ?>

                    </div>

                </div>
                <div class='comments_reply' style=" margin-left: 50px;">
                    <?php if (isset($this->reply_form)) {
                        $this->reply_form->populate(array(
                            'comment_identity' => $this->comment->getIdentity(),
                            'type' => $this->subject->getType(),
                            'identity' => $this->subject->getIdentity(),
                            'comment_owner' => $comment->poster_id
                        ));
                        echo $this->reply_form->setCommentIdentity($comment->comment_id)->setAttribs(array('id' => 'hecomment-form-' . $comment->comment_id, 'class' => 'hecomment-form-class', 'style' => 'display:none;'))->render();
                    }?>
                </div>

            </li>
        <?php
        }
    } ?>

    <?php if ($replyhide): ?>
        <a href="javascript:void(0)" id="hide_reply_comments_button-<?php echo $this->comment->comment_id; ?>"
           onclick="Hecomment.hideReplyComments(<?php echo $this->comment->comment_id; ?>, this);" style="display: none"
           class="hecomment_reply_comments_hide_button hei-reply">
            <?php echo $this->translate(array("HECOMMENT_Hide reply", "HECOMMENT_Hide replies", $count_comments), $count_comments); ?>
        </a>
    <?php endif; ?>
<?php
}
?>