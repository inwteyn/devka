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

<ul class="generic_list_widget">
  <?php foreach( $this->paginator as $item ): ?>
  <?php
  if( $item['type'] == 'page')
    $blog = Engine_Api::_()->getItem('pageblog', $item['blog_id']);
  else
    $blog = Engine_Api::_()->getItem('blog', $item['blog_id']);
  ?>
  <li>
    <div class="photo">
      <?php echo $this->htmlLink($blog->getHref(), $this->itemPhoto($blog->getOwner(), 'thumb.icon'), array('class' => 'thumb')) ?>
    </div>
    <div class="info">
      <div class="title">
        <?php echo $this->htmlLink($blog->getHref(), $blog->getTitle()) ?>
      </div>
      <div class="stats">
        <?php echo $this->timestamp($blog->creation_date) ?>
      </div>
      <div class="owner">
        <?php
        $owner = $blog->getOwner();?>
        <?php if( $item['type'] == 'page' && (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('page.show.owner', 0) == 1 || Engine_Api::_()->getItem('page', $blog->page_id)->getOwner() != $blog->getOwner())) : ?>
          <?php echo $this->translate('Posted by %1$s', $this->htmlLink($owner->getHref(), $owner->getTitle()));?>
        <?php elseif ($item['type'] == 'page'):?>
          <?php echo $this->translate('Posted by %1$s', $this->htmlLink(Engine_Api::_()->getItem('page', $blog->page_id)->getHref(), Engine_Api::_()->getItem('page', $blog->page_id)->getTitle()));?>
        <?php else:?>
          <?php echo $this->translate('Posted by %1$s', $this->htmlLink($owner->getHref(), $owner->getTitle()));?>
        <?php endif;?>
        <?php if( $item['type'] == 'page' ) {
          echo '<br/>';
          echo $this->translate('On page ');
          echo $this->htmlLink($blog->getPage()->getHref(), $blog->getPage()->getTitle());
        }
        ?>
      </div>
    </div>
    <div class="description">
      <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 300) ?>
    </div>
  </li>
  <?php endforeach; ?>
</ul>