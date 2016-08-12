<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecomment
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     Bolot
 */
?>
<?php
$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Hecomment/externals/scripts/core.js');

$prepare_local = false;

$select = Engine_Api::_()->wall()->getTagSuggest($this->viewer());
$paginator = Zend_Paginator::factory($select);

if ($paginator->getTotalItemCount() < 500) {

    $prepare_local = array();
    $paginator->setItemCountPerPage(499);
    foreach (Engine_Api::_()->wall()->getItems($paginator->getCurrentItems()) as $item) {

        $prepare_local[] = array(
            'type' => $item->getType(),
            'id' => $item->getIdentity(),
            'guid' => $item->getGuid(),
            'label' => $item->getTitle(),
            'photo' => $this->itemPhoto($item, 'thumb.icon'),
            'url' => $item->getHref(),
        );

        if ($item->getType() == 'user') {
            $prepare_local_users[] = array(
                'type' => $item->getType(),
                'id' => $item->getIdentity(),
                'guid' => $item->getGuid(),
                'label' => $item->getTitle(),
                'photo' => $this->itemPhoto($item, 'thumb.icon'),
                'url' => $item->getHref(),
                'username' => $item->username,
            );
        }

    }

}

?>

<?php $this->headTranslate(array(
    'Are you sure you want to delete this?',
)); ?>

<?php if (!$this->page): ?>
    <div class='comments' id="hecomments">
<?php endif; ?>
    <div class='comments_options'>
        <span><?php echo $this->translate(array('%s comment', '%s comments', $this->comments->getTotalItemCount()), $this->locale()->toNumber($this->comments->getTotalItemCount())) ?></span>

        <?php if (isset($this->form)): ?>
            - <a href='javascript:void(0);'
                 onclick="Hecomment.core.comments.show_hide_comment_form(<?php echo $this->subject->getIdentity(); ?>)"><?php echo $this->translate('Post Comment') ?></a>
        <?php endif; ?>

        <?php if ($this->viewer()->getIdentity() && $this->canComment): ?>
            <?php if ($this->subject()->likes()->isLike($this->viewer())): ?>
                - <a href="javascript:void(0);"
                     onclick="Hecomment.core.comments.unlike('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>')"><?php echo $this->translate('Unlike This') ?></a>
            <?php else: ?>
                - <a href="javascript:void(0);"
                     onclick="Hecomment.core.comments.like('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>')"><?php echo $this->translate('Like This') ?></a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <ul>

        <?php if ($this->likes->getTotalItemCount() > 0): // LIKES ------------- ?>
        <li>
            <?php if ($this->viewAllLikes || $this->likes->getTotalItemCount() <= 3): ?>
                <?php $this->likes->setItemCountPerPage($this->likes->getTotalItemCount()) ?>
                <div></div>
                <div class="comments_likes">
                    <?php echo $this->translate(array('%s likes this', '%s like this', $this->likes->getTotalItemCount()), $this->fluentList($this->subject()->likes()->getAllLikesUsers())) ?>
                </div>
            <?php else: ?>
                <div></div>
                <div class="comments_likes">
                    <?php echo $this->htmlLink('javascript:void(0);',
                        $this->translate(array('%s person likes this', '%s people like this', $this->likes->getTotalItemCount()), $this->locale()->toNumber($this->likes->getTotalItemCount())),
                        array('onclick' => 'en4.core.comments.showLikes("' . $this->subject()->getType() . '", "' . $this->subject()->getIdentity() . '");')
                    ); ?>
                </div>
            <?php endif; ?>
            <?php endif; ?>

            <?php if ($this->comments->getTotalItemCount() > 0): // COMMENTS ------- ?>

            <?php if ($this->page && $this->comments->getCurrentPageNumber() > 1): ?>
        <li>
            <div></div>
            <div class="comments_viewall">
                <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View previous comments'), array(
                    'onclick' => 'Hecomment.core.comments.loadComments("' . $this->subject()->getType() . '", "' . $this->subject()->getIdentity() . '", "' . ($this->page - 1) . '")'
                )) ?>
            </div>
        </li>
    <?php endif; ?>

    <?php if (!$this->page && $this->comments->getCurrentPageNumber() < $this->comments->count()): ?>
        <li>
            <div></div>
            <div class="comments_viewall">
                <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View more comments'), array(
                    'onclick' => 'Hecomment.core.comments.loadComments("' . $this->subject()->getType() . '", "' . $this->subject()->getIdentity() . '", "' . ($this->comments->getCurrentPageNumber()) . '")'
                )) ?>
            </div>
        </li>
    <?php endif; ?>

    <?php // Iterate over the comments backwards (or forwards!)
    $comments = $this->comments->getIterator();
    if ($this->page):
        $i = 0;
        $l = count($comments) - 1;
        $d = 1;
        $e = $l + 1;
    else:
        $i = count($comments) - 1;
        $l = count($comments);
        $d = -1;
        $e = -1;
    endif;
    for (; $i != $e; $i += $d):
        $comment = $comments[$i];
        $poster = $this->item($comment->poster_type, $comment->poster_id);
        $canDelete = ($this->canDelete || $poster->isSelf($this->viewer()));
        ?>
        <li id="comment-<?php echo $comment->comment_id ?>">
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

                    - <a href="javascript:void(0)"
                         onclick="Hecomment.core.comments.show_hide_comment_form(<?php echo $comment->comment_id ?>)">
                        <?php echo $this->translate('Reply') ?>
                    </a>
                    <?php if ($canDelete): ?>
                        -
                        <a href="javascript:void(0);"
                           onclick="Hecomment.core.comments.editComment('<?php echo $comment->comment_id ?>')">
                            <?php echo $this->translate('Edit') ?>
                        </a>
                        -
                        <a href="javascript:void(0);"
                           onclick="Hecomment.core.comments.deleteComment('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>', '<?php echo $comment->comment_id ?>')">
                            <?php echo $this->translate('Delete') ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class='comments_reply' style=" margin-left: 50px;">

                <?php  if (isset($this->reply_form)) {
                    $this->reply_form->populate(array(
                        'comment_identity' => $comment->comment_id,
                        'type' => $this->subject->getType(),
                        'identity' => $this->subject->getIdentity(),
                        'comment_owner' => $comment->poster_id
                    ));
                    echo $this->reply_form->setCommentIdentity($comment->comment_id)->setAttribs(array('id' => 'hecomment-form-' . $comment->comment_id, 'class' => 'hecomment-form-class', 'style' => 'display:none;'))->render();
                }

                ?>
                <ul>
                    <?php
                    $this->comment = $comment;
                    echo $this->render('_comments.tpl'); ?>

                </ul>


            </div>

        </li>
    <?php endfor; ?>

    <?php if ($this->page && $this->comments->getCurrentPageNumber() < $this->comments->count()): ?>
        <li>
            <div></div>
            <div class="comments_viewall">
                <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View later comments'), array(
                    'onclick' => 'Hecomment.core.comments.loadComments("' . $this->subject()->getType() . '", "' . $this->subject()->getIdentity() . '", "' . ($this->page + 1) . '")'
                )) ?>
            </div>
        </li>
    <?php endif; ?>

    <?php endif; ?>

    </ul>
    <script type="text/javascript">
        en4.core.runonce.add(function () {
            mention = new Hecomment.mention();

            mention.setOptions({
                <?php if ($prepare_local !== false){?>
                'suggestProto': 'local',
                'suggestUsers': <?php echo Zend_Json::encode($prepare_local_users) ?>
                    <?php }else{ ?>
                    'suggestProto':'request.json',
                'suggestUsers': []
            <?php }?>
        });
        window.suggestUsers = <?php echo Zend_Json::encode($prepare_local_users) ?>;

        var CommentLikesTooltips;
        // Scroll to comment
        if (window.location.hash != '') {
            var hel = $(window.location.hash);
            if (hel) {
                window.scrollTo(hel);
            }
        }
        // Add hover event to get likes
        $$('.comments_comment_likes').addEvent('mouseover', function (event) {
            var el = $(event.target);
            if (!el.retrieve('tip-loaded', false)) {
                el.store('tip-loaded', true);
                el.store('tip:title', '<?php echo $this->translate('Loading...') ?>');
                el.store('tip:text', '');
                var id = el.get('id').match(/\d+/)[0];
                // Load the likes
                var url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'comment', 'action' => 'get-likes'), 'default', true) ?>';
                var req = new Request.JSON({
                    url: url,
                    data: {
                        format: 'json',
                        type: 'core_comment',
                        id: id
                        //type : '<?php //echo $this->subject()->getType() ?>',
                        //id : '<?php //echo $this->subject()->getIdentity() ?>',
                        //comment_id : id
                    },
                    onComplete: function (responseJSON) {
                        el.store('tip:title', responseJSON.body);
                        el.store('tip:text', '');
                        CommentLikesTooltips.elementEnter(event, el); // Force it to update the text
                    }
                });
                req.send();
            }
        });
        // Add tooltips
        CommentLikesTooltips = new Tips($$('.comments_comment_likes'), {
            fixed: true,
            className: 'comments_comment_likes_tips',
            offset: {
                'x': 48,
                'y': 16
            }
        });
        // Enable links
        $$('.comments_body').enableLinks();

        $($('hecomment-form-<?php echo $this->subject->getIdentity(); ?>').body).autogrow();
        Hecomment.core.comments.attachCreateComment($('hecomment-form-<?php echo $this->subject->getIdentity(); ?>'));

        $$('.hecomment-form-class').each(function (element) {
            $(element.body).autogrow();
            Hecomment.core.comments.attachCreateComment(element);
        });
        mention.second_activate();
        })
    </script>
<?php if (isset($this->form)) echo $this->form->setAttribs(array('id' => 'hecomment-form-' . $this->subject->getIdentity(), 'class' => 'hecomment-form-class', 'style' => 'display:none;'))->render() ?>

<?php if (!$this->page): ?>
    </div>
<?php endif; ?>