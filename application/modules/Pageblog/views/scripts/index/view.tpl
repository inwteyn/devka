<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<?php
  $viewer = Engine_Api::_()->user()->getViewer();
?>

<div class="pageblog_view_header">
  <span>
    <?php if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('page.show.owner', 0) == 1 || Engine_Api::_()->getItem('page', $this->blog->page_id)->getOwner() != $this->blog->getOwner()):?>
      <?php echo $this->translate('%1$s\'s Blog', $this->htmlLink($this->owner->getHref(), $this->owner->getTitle())); ?>
    <?php else:?>
      <?php echo $this->translate('%1$s\'s Blog', $this->htmlLink(Engine_Api::_()->getItem('page', $this->blog->page_id)->getHref(), Engine_Api::_()->getItem('page', $this->blog->page_id)->getTitle())); ?>
    <?php endif;?>
  </span>
<?php if (!$this->isAllowedPost): ?>
<div class="backlink_wrapper">
	<a class="backlink" href="javascript:page_blog.list()"><?php echo $this->translate('Back To Blogs'); ?></a>
</div>
<?php endif; ?>

<div class="clr"></div>

</div>

<ul class='blogs_entrylist'>
  <li>
    <div class='blogs_view_photo'>
      <?php echo $this->itemPhoto($this->blog, 'thumb.profile'); ?>
    </div>
    <div class="pageblog_details">
      <h3><?php echo $this->blog->getTitle() ?></h3>
      <div class="page-misc">
        <div class="page-misc-date">
          <?php echo $this->translate("Posted %s", $this->timestamp($this->blog->creation_date)); ?>
        </div>
        <?php if (count($this->blogTags)):?>
          <div class="page-tag">
            <div class="tags">
              <?php foreach ($this->blogTags as $tag): ?>
                <a href='javascript:void(0);' onclick="page_search.search_by_tag(<?php echo $tag->getTag()->tag_id; ?>);">#<?php echo $tag->getTag()->text ?></a>&nbsp;
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
      <br>
      <div class="pageblog_options">
        <?php if( $viewer->getIdentity() ):?>
          <?php echo $this->htmlLink($this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => 'pageblog', 'id' => $this->blog->getIdentity(), 'format' => 'smoothbox'), 'default'), $this->translate('Share'), array('class' => 'buttonlink smoothbox icon_comments'))?>
        <?php endif;?>
      </div>
    </div>
    <div class="blog_entrylist_entry_body">
      <?php echo nl2br($this->blog->body); ?>
    </div>
  </li>
</ul>

<?php if (Engine_Api::_()->getDbTable('modules' ,'hecore')->isModuleEnabled('wall')): ?>
  <?php echo $this->wallComments($this->blog, $this->viewer()); ?>
<?php else: ?>
  <div class="comments" id="pageblog_comments"></div>
<?php endif;?>