<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: view.tpl 8164 2011-01-07 01:50:57Z steve $
 * @author     John
 */
?>

<div class="layout_content">
  <div class="touch-navigation">
    <div class="navigation-header">
      <h3>
      <?php echo $this->htmlLink(array('route'=>'forum_general'), $this->translate("Forums"), array('class' => 'touchajax'));?>
        &#187; <?php echo $this->translate($this->forum->getTitle()) ?>
      </h3>
    </div>
  </div>
  <div style="height: 10px"></div>

  <?php if( count($this->paginator) > 0 ): ?>
    <ul class="forum_topics">
      <li class="forum_option_header">
        <?php if( $this->canPost ): ?>
          <div class="forum_header_options">
            <?php echo $this->htmlLink($this->forum->getHref(array(
              'action' => 'topic-create',
            )), $this->translate('Post New Topic'), array(
              'class' => 'buttonlink icon_forum_post_new touchajax'
            )) ?>
          </div>
        <?php endif; ?>
          <div class="forum_header_pages">
            <?php echo $this->paginationControl($this->paginator);?>
          </div>
        <div class="forum_header_moderators">
          <?php echo $this->translate('Moderators:');?>
          <?php echo $this->fluentList($this->moderators) ?>
        </div>
      </li>
      <?php foreach( $this->paginator as $i => $topic ):
        $last_post = $topic->getLastCreatedPost();
        if( $last_post ) {
          $last_user = $this->user($last_post->user_id);
        } else {
          $last_user = $this->user($topic->user_id);
        }
        ?>
        <li class="forum_nth_<?php echo $i % 2 ?>">
          <div class="forum_topics_lastpost">
            <?php if( $last_post):
              list($openTag, $closeTag) = explode('-----', $this->htmlLink($last_post->getHref(array('slug' => $topic->getSlug())), '-----', array('class' => 'touchajax')));
              ?>
              <?php echo $this->htmlLink($last_post->getHref(), $this->itemPhoto($last_user, 'thumb.icon'), array('class' => 'touchajax')) ?>
              <span class="forum_topics_lastpost_info">
                <?php echo $this->translate(
                  '%1$sLast post%2$s',
                  $openTag,
                  $closeTag
                )?>
<!--                --><?php //echo $this->timestamp($topic->modified_date, array('class' => 'forum_topics_lastpost_date')) ?>
              </span>
            <?php endif; ?>
          </div>
          <div class="forum_topics_views forum_topics_replies">
            <span></span>
            <span>
              <?php echo $this->translate(array('%1$s %2$s view', '%1$s %2$s views', $topic->view_count), $this->locale()->toNumber($topic->view_count), '</span><span>') ?>
            </span>
          <span>
            <?php echo $this->translate(array('%1$s %2$s reply', '%1$s %2$s replies', $topic->post_count-1), $this->locale()->toNumber($topic->post_count-1), '</span><span>') ?>
          </span>
        </div>
        <div class="forum_topics_title">
          <h3<?php if( $topic->closed ): ?> class="closed"<?php endif; ?><?php if( $topic->sticky ): ?> class="sticky"<?php endif; ?>>
            <?php echo $this->htmlLink($topic->getHref(), $topic->getTitle(), array('class' => 'touchajax'));?>
          </h3>
          <?php echo $this->pageLinks($topic, $this->forum_topic_pagelength, null, 'forum_pagelinks') ?>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
  <?php else: ?>
    <ul class="forum_topics">
      <li class="forum_option_header">
        <?php if( $this->canPost ): ?>
          <div class="forum_header_options">
            <?php echo $this->htmlLink($this->forum->getHref(array(
              'action' => 'topic-create',
            )), $this->translate('Post New Topic'), array(
              'class' => 'buttonlink icon_forum_post_new touchajax'
            )) ?>
          </div>
        <?php endif; ?>
        <div class="forum_header_moderators">
          <?php echo $this->translate('Moderators:');?>
          <?php echo $this->fluentList($this->moderators) ?>
        </div>
      </li>
    </ul>
  <?php endif; ?>
  <div class="forum_pages">
    <?php echo $this->paginationControl($this->paginator);?>
</div></div>