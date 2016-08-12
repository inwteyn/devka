<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-02-20 15:35 Ulan T $
 * @author     Ulan T
 */
?>

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<ul class="blogs_browse">
  <?php foreach( $this->paginator as $item ): ?>
    <?php if($item['type'] == 'blog') : ?>
      <?php $blog = Engine_Api::_()->getItem('blog', $item['blog_id']);?>
    <?php else: ?>
      <?php $blog = Engine_Api::_()->getItem('pageblog', $item['blog_id']);?>
    <?php endif; ?>
      <li>
          <div class='blogs_browse_photo'>
            <?php if($item['type'] == 'blog') :?>
            <?php echo $this->htmlLink($blog->getOwner()->getHref(), $this->itemPhoto($blog->getOwner(), 'thumb.icon')) ?>
            <?php else:?>
            <?php echo $this->htmlLink($blog->getHref(), $this->itemPhoto($blog, 'thumb.icon')) ?>
            <?php endif;?>
          </div>

        <div class='blogs_browse_info'>
          <span class='blogs_browse_info_title'>
            <h3><?php echo $this->htmlLink($blog->getHref(), $blog->getTitle()) ?></h3>
          </span>
          <p class='blogs_browse_info_date'>
            <?php echo $this->translate('Posted');?>
            <?php echo $this->timestamp(strtotime($blog->creation_date)) ?>
            <?php echo $this->translate('by');?>
            <?php
            $owner = $blog->getOwner();?>
            <?php if( $item['type'] == 'page' && (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('page.show.owner', 0) == 1 || Engine_Api::_()->getItem('page', $blog->page_id)->getOwner() != $blog->getOwner())) : ?>
              <?php echo $this->htmlLink($owner->getHref(), $owner->getTitle());?>
            <?php elseif ($item['type'] == 'page'):?>
              <?php echo $this->htmlLink(Engine_Api::_()->getItem('page', $blog->page_id)->getHref(), Engine_Api::_()->getItem('page', $blog->page_id)->getTitle());?>
            <?php else:?>
              <?php echo $this->htmlLink($owner->getHref(), $owner->getTitle());?>
            <?php endif;?>
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

<?php elseif( $this->category || $this->show == 2 || $this->search ): ?>
<div class="tip">
    <span>
      <?php echo $this->translate('Nobody has written a blog entry with that criteria.');?>
      <?php if (TRUE): // @todo check if user is allowed to create a poll ?>
      <?php echo $this->translate('Be the first to %1$swrite%2$s one!', '<a href="'.$this->url(array('action' => 'create'), 'blog_general').'">', '</a>'); ?>
      <?php endif; ?>
    </span>
</div>

<?php else:?>
<div class="tip">
    <span>
      <?php echo $this->translate('Nobody has written a blog entry yet.'); ?>
    </span>
</div>
<?php endif; ?>

<?php echo $this->paginationControl($this->paginator, null, null, array(
  'pageAsQuery' => true,
  'query' => $this->formValues,
  //'params' => $this->formValues,
)); ?>
