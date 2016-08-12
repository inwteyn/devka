<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manage.tpl 2012-02-20 15:35 Ulan T $
 * @author     Ulan T
 */
?>
<div class="layout_right">
  <?php  echo $this->form->render($this);?>
</div>

<div class="layout_middle">
<?php if ($this->paginator->getTotalItemCount() > 0): ?>
<ul class="blogs_browse">
  <?php foreach ($this->paginator as $item): ?>
  <?php if($item['type'] == 'blog') : ?>
    <?php $blog = Engine_Api::_()->getItem('blog', $item['blog_id']);?>
    <?php else: ?>
    <?php $blog = Engine_Api::_()->getItem('pageblog', $item['blog_id']);?>
    <?php endif; ?>
  <li>
      <div class='blogs_browse_photo'>
        <?php if($item['type'] == 'blog') :?>
        <?php echo $this->htmlLink($blog->getOwner()->getHref(), $this->itemPhoto($blog->getOwner(), 'thumb.normal')) ?>
        <?php else:?>
        <?php echo $this->htmlLink($blog->getHref(), $this->itemPhoto($blog, 'thumb.normal')) ?>
        <?php endif;?>
      </div>
    <div class="blogs_browse_info">
      <div class="blogs_browse_info_title">
        <?php echo $this->htmlLink($blog->getHref(), $blog->getTitle()); ?>
        <div class="blogs_options">
          <?php if( $item['type'] == 'page' ) : ?>
            <?php echo $this->htmlLink($blog->getHref(), $this->htmlImage($this->baseUrl() . '/application/modules/Pageblog/externals/images/edit16.png', '', array('border' => 0)), array('title' => $this->translate('edit')) ); ?>
            <?php echo $this->htmlLink(array('route' => 'page_blogs', 'action' => 'delete', 'pageblog_id' => $blog->getIdentity(), 'format' => 'smoothbox'), $this->htmlImage($this->baseUrl() . '/application/modules/Pageblog/externals/images/delete16.png', '', array('border' => 0)), array('title' => $this->translate('delete'), 'class' => 'smoothbox')); ?>
          <?php else : ?>

            <?php echo $this->htmlLink(array(
            'action' => 'edit',
            'blog_id' => $blog->getIdentity(),
            'route' => 'blog_specific',
            'reset' => true,
          ), $this->htmlImage($this->baseUrl() . '/application/modules/Pageblog/externals/images/edit16.png', '', array('border' => 0)), array('title' => $this->translate('edit')) ); ?>

            <?php echo $this->htmlLink(array(
            'route' => 'default',
            'module' => 'blog',
            'controller' => 'index',
            'action' => 'delete',
            'blog_id' => $blog->getIdentity(),
            'format' => 'smoothbox'), $this->htmlImage($this->baseUrl() . '/application/modules/Pageblog/externals/images/delete16.png', '', array('border' => 0)), array('title' => $this->translate('delete'), 'class' => 'smoothbox')); ?>
          <?php endif; ?>
        </div>
      </div>
      <p class="blogs_browse_info_date">
        <?php echo $this->translate('Posted');?> <?php echo $this->timestamp($blog->creation_date); ?>
        <br/>
        <?php echo $this->translate('By ');?>
        <?php echo $this->htmlLink($blog->getOwner()->getHref(), $blog->getOwner()->getTitle());?>
        <?php if($item['type'] == 'page') : ?>
        -
        <?php echo $this->translate('On page ');?>
        <?php echo $this->htmlLink($blog->getPage()->getHref(), $blog->getPage()->getTitle()) ?>
        <?php endif; ?>
      </p>
      <p class="blogs_browse_info_blurb">
        <?php echo Engine_String::strip_tags(Engine_String::substr($blog->body, 0, 350)); if (Engine_String::strlen($blog->body)>349) echo $this->translate("..."); ?>
      </p>
    </div>
  </li>
  <?php endforeach; ?>
</ul>

<?php if( $this->paginator->count() > 1 ): ?>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => true,
      'query' => $this->formValues,
      //'params' => $this->formValues,
    )); ?>
  <?php endif; ?>
<?php else: ?>
<div class="tip">
    <span>
      <?php echo $this->translate('You don\'t have any blogs.');?>
    </span>
</div>
<?php endif; ?>
</div>
