<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: view.tpl 8994 2011-06-16 23:31:25Z john $
 * @author     John
 */
?>
<?php $this->headScript()
  ->appendFile($this->baseUrl() . '/application/modules/Touch/modules/Forum/externals/scripts/core.js')
?>

<script type="text/javascript">
en4.core.runonce.add(function() {
  $$('.forum_topic_posts_info_body').enableLinks();

  // Scroll to the selected post
  var post_id = <?php echo sprintf('%d', $this->post_id) ?>;
  if( post_id > 0 ) {
    window.scrollTo(0, $('forum_post_' + post_id).getPosition().y);
  }
});
/*
    var scroll = new Fx.Scroll(document.body, {
      wait: false,
      duration: 1500,
      offset: {'x': -200, 'y': -50},
      transition: Fx.Transitions.Quad.easeInOut
    });
*/
var Replier = new PostReply();
 // $('photo-wrapper').setStyle('display', 'none');
</script>

<div class="layout_content">
  <div class="touch-navigation">
    <div class="navigation-header">
      <h3>
      <?php echo $this->htmlLink(array('route'=>'forum_general'), $this->translate("Forums"), array('class' => 'touchajax'));?>
        &#187; <?php echo $this->htmlLink(array('route'=>'forum_forum', 'forum_id'=>$this->forum->getIdentity()), $this->forum->getTitle(), array('class' => 'touchajax'));?>
      </h3>
    </div>
  </div>

  <div style="height: 10px"></div>

  <div class="forum_topic_title_wrapper">
    <div class="forum_topic_title">
      <h3><?php echo $this->topic->getTitle() ?></h3>
    </div>
    <div class="forum_topic_title_options">
      <?php echo $this->htmlLink($this->forum->getHref(), $this->translate('Back To Topics'), array(
        'class' => 'buttonlink icon_back touchajax'
      )) ?>
      <?php if( $this->canPost ): ?>
        <a class="buttonlink icon_forum_post_reply" onclick="Replier.quickReply();">
          <?php echo $this->translate('Post Reply'); ?>
        </a>
      <?php endif; ?>
      <?php if( $this->viewer->getIdentity() ): ?>
        <?php if( !$this->isWatching ): ?>
          <?php echo $this->htmlLink($this->url(array('action' => 'watch', 'watch' => '1')), $this->translate('Watch Topic'), array(
            'class' => 'buttonlink icon_forum_topic_watch'
          )) ?>
        <?php else: ?>
          <?php echo $this->htmlLink($this->url(array('action' => 'watch', 'watch' => '0')), $this->translate('Stop Watching Topic'), array(
            'class' => 'buttonlink icon_forum_topic_unwatch'
          )) ?>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>

  <?php if( $this->topic->closed ): ?>
    <div class="forum_discussions_thread_options_closed">
      <?php echo $this->translate('This topic has been closed.');?>
    </div>
  <?php endif; ?>

  <div class="forum_topic_pages">
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'params' => array(
        'post_id' => null,
      ),
    )); ?>
  </div>


  <script type="text/javascript">
  </script>

  <ul class="forum_topic_posts">
    <?php foreach( $this->paginator as $i => $post ): ?>
      <?php $user = $this->user($post->user_id); ?>
      <?php $signature = $post->getSignature(); ?>
      <?php $isModeratorPost = $this->forum->isModerator($post->getOwner()) ?>
      <li id="forum_post_<?php echo $post->post_id ?>" class="forum_post_item forum_nth_<?php echo $i % 2 ?><?php if( $isModeratorPost ): ?> forum_moderator_post<?php endif ?>">
        <div class="forum_topic_posts_author">
          <div class="forum_topic_posts_author_name">
          <?php echo $user->__toString(); ?>
          </div>
          <div class="forum_topic_posts_author_photo">
          <?php echo $this->itemPhoto($user); ?>
          </div>
          <ul class="forum_topic_posts_author_info">
            <?php if( $post->user_id != 0 ): ?>
              <?php if( $post->getOwner() ): ?>
                <?php if( $isModeratorPost ): ?>
                  <li class="forum_topic_posts_author_info_title"><?php echo $this->translate('Moderator') ?></li>
                <?php endif; ?>
              <?php endif; ?>
            <?php endif; ?>

            <?php if( $signature ): ?>
              <li>
                <?php echo $signature->post_count; ?>
                <?php echo $this->translate('posts');?>
              </li>
            <?php endif; ?>
          </ul>
        </div>
        <div class="forum_topic_posts_info">
          <div class="forum_topic_posts_info_top">
            <div class="forum_topic_posts_info_top_anchor">
              <a href="<?php echo $post->getHref() ?>" class="touchajax">
                &nbsp;
              </a>
            </div>
            <div class="forum_topic_posts_info_top_date">
              <?php echo $this->locale()->toDateTime(strtotime($post->creation_date)) ?>
            </div>
            <div class="forum_topic_posts_info_top_options">
              <?php if( $this->canPost && $this->form ): ?>
              <a class="buttonlink icon_forum_post_quote" onclick="Replier.quoteReply('<?php echo $post->post_id ?>');">
                <?php echo $this->translate('Quote'); ?>
              </a>
              <?php endif;?>
              <?php if( $this->canEdit ):?>
                <a href="<?php echo $this->url(array('post_id'=>$post->getIdentity(), 'action'=>'edit'), 'forum_post'); ?>" class="buttonlink icon_forum_post_edit touchajax"><?php echo $this->translate('Edit');?></a>
              <?php elseif( $post->user_id != 0 && $post->isOwner($this->viewer) && !$this->topic->closed ): ?>
                <?php if( $this->canEdit_Post ): ?>
                  <a href="<?php echo $this->url(array('post_id'=>$post->getIdentity(), 'action'=>'edit'), 'forum_post'); ?>" class="buttonlink icon_forum_post_edit touchajax"><?php echo $this->translate('Edit');?></a>
                <?php endif; ?>
                <?php if( $this->canDelete_Post ): ?>
                  <a href="<?php echo $this->url(array('post_id'=>$post->getIdentity(), 'action'=>'delete'), 'forum_post');?>" class="buttonlink smoothbox icon_forum_post_delete touchajax"><?php echo $this->translate('Delete');?></a>
                <?php endif; ?>
              <?php endif; ?>
            </div>
          </div>
          <div class="forum_topic_posts_info_body">
            <?php
              $body = $post->body;
              $doNl2br = false;
              if( strip_tags($body) == $body ) {
                $body = nl2br($body);
              }
              if( !$this->decode_html && $this->decode_bbcode ) {
                $body = $this->BBCode($body, array('link_no_preparse' => true));
              }
              echo $body;
            ?>
            <?php if( $post->edit_id && !empty($post->modified_date) ):?>
              <br />
              <i>
                <?php echo $this->translate('This post was edited by %1$s at %2$s', $this->user($post->edit_id)->__toString(), $this->locale()->toDateTime(strtotime($post->modified_date))); ?>
              </i>
            <?php endif;?>
          </div>
          <?php if ($post->file_id):?>
            <div class="forum_topic_posts_info_photo">
              <?php echo $this->itemPhoto($post, null, '', array('class'=>'forum_post_photo'));?>
            </div>
          <?php endif;?>
        </div>
      </li>
    <?php endforeach;?>
  </ul>

  <div class="forum_topic_pages">
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'params' => array(
        'post_id' => null,
      ),
    )); ?>
  </div>

  <?php if( $this->canPost && $this->form ): ?>
    <?php echo $this->form->setAttrib('style', 'display: none')->render(); ?>
  <?php endif; ?>
</div>
