<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 8221 2011-01-15 00:24:02Z john $
 * @author     Char
 */
$urlParams = array(
  'module' => 'core',
  'controller' => 'widget',
  'action' => 'index',
  'content_id' => $this->identity,
  'subject' => $this->subject()->getGuid(),
  'format' => 'html'
);
?>
<?php $this->headScript()
  ->appendFile($this->baseUrl() . '/application/modules/Touch/modules/Forum/externals/scripts/core.js')
?>

<div id="widget_content">
  <div class="search">

    <?php echo $this->paginationControl($this->paginator, null,
        array('pagination/filter.tpl', 'touch'),
        array(
          'search'=>$this->form->getElement('search')->getValue(),
          'filter_default_value'=>$this->translate('TOUCH_Search Forum Posts'),
          'filterUrl'=> $this->url($urlParams, 'default', true),
          'filterOptions' => array(
            'replace_content' => 'widget_content',
            'noChangeHash' => 1,
          ),
          'pageUrlParams' => $urlParams
        )
    ); ?>
  </div>
  <div id="filter_block">

    <ul class="forum_topic_posts" id="forum_topic_posts">
      <?php foreach( $this->paginator as $post ):
        if( !isset($signature) ) $signature = $post->getSignature();
        $topic = $post->getParent();
        $forum = $topic->getParent();
        ?>
        <li>
          <div class="forum_topic_posts_info">
            <div class="forum_topic_posts_info_top">
              <div class="forum_topic_posts_info_top_date">
                <?php echo $this->locale()->toDateTime(strtotime($post->creation_date));?>
              </div>
              <div class="forum_topic_posts_info_top_parents">
                <?php echo $this->translate('Topic %1$s', $topic->__toString()) ?>
                <?php echo $this->translate('Forum %1$s', $forum->__toString()) ?>
              </div>
            </div>
            <div class="forum_topic_posts_info_body">
              <?php if( $this->decode_bbcode ) {
                echo nl2br($this->BBCode($post->body));
              } else {
                echo $post->body;
              } ?>
              <?php if( $post->edit_id ): ?>
                <i>
                  <?php echo $this->translate('Edited by %1$s at %2$s', $this->user($post->edit_id)->__toString(), $this->locale()->toDateTime(strtotime($post->creation_date))); ?>
                </i>
              <?php endif;?>
            </div>
            <?php if( $post->file_id ): ?>
              <div class="forum_topic_posts_info_photo">
                <?php echo $this->itemPhoto($post, null, '', array('class'=>'forum_post_photo'));?>
              </div>
            <?php endif;?>
          </div>
        </li>
      <?php endforeach;?>
    </ul>
  </div>
</div>