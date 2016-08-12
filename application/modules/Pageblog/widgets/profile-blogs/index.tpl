<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */
?>

<script type="text/javascript">
  en4.core.runonce.add(function(){

  <?php if( !$this->renderOne ): ?>
    var anchor = $('profile_pageblogs').getParent();
    $('profile_pageblogs_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('profile_pageblogs_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('profile_pageblogs_previous').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
        }
      }), {
        'element' : anchor
      })
    });

    $('profile_pageblogs_next').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
        }
      }), {
        'element' : anchor
      })
    });
    <?php endif; ?>
  });
</script>

<ul id="profile_pageblogs" class="blogs_browse">
  <?php foreach( $this->paginator as $item ): ?>
  <?php if($item['type'] == 'blog') : ?>
    <?php $blog = Engine_Api::_()->getItem('blog', $item['blog_id']);?>
    <?php else: ?>
    <?php $blog = Engine_Api::_()->getItem('pageblog', $item['blog_id']);?>
    <?php endif; ?>
  <li>
    <div class='blogs_browse_photo'>
      <?php echo $this->htmlLink($blog->getOwner()->getHref(), $this->itemPhoto($blog->getOwner(), 'thumb.icon')) ?>
    </div>

    <div class='blogs_browse_info'>
          <span class='blogs_browse_info_title'>
            <h3><?php echo $this->htmlLink($blog->getHref(), $blog->getTitle()) ?></h3>
          </span>
      <p class='blogs_browse_info_date'>
        <?php echo $this->translate('Posted');?>
        <?php echo $this->timestamp(strtotime($blog->creation_date)) ?>
        <?php echo $this->translate('by');?>
        <?php echo $this->htmlLink($blog->getOwner()->getHref(), $blog->getOwner()->getTitle()) ?>
        <?php if($item['type'] == 'page') : ?>
        <br/>
        <?php echo $this->translate('On page ');?>
        <?php echo $this->htmlLink($blog->getPage()->getHref(), $blog->getPage()->getTitle()) ?>
        <?php endif; ?>
      </p>
      <p class='blogs_browse_info_blurb'>
        <?php echo $this->string()->truncate($this->string()->stripTags($blog->body), 300) ?>
      </p>
    </div>
  </li>
  <?php endforeach; ?>
</ul>

<div>
  <div id="profile_pageblogs_previous" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
    'onclick' => '',
    'class' => 'buttonlink icon_previous'
  )); ?>
  </div>
  <div id="profile_pageblogs_next" class="paginator_next">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
    'onclick' => '',
    'class' => 'buttonlink_right icon_next'
  )); ?>
  </div>
</div>