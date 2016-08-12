<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: list_edit.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<?php if ($this->blogs->getTotalItemCount() > 0): ?>
	<ul class="blogs_browse">
    <?php foreach ($this->blogs as $blog): ?>
      <li>
          <div class='blogs_browse_photo'>
            <?php echo $this->htmlLink($blog->getHref(), $this->itemPhoto($blog, 'thumb.normal'), array('onclick' => "page_blog.view({$blog->getIdentity()}); return false;")) ?>
          </div>
        <div class="blogs_browse_info">
          <div class="blogs_browse_info_title">
            <?php echo $this->htmlLink($blog->getHref(),
                                       $blog->getTitle(),
                                       array('onclick' => "page_blog.view({$blog->getIdentity()}); return false;"));
            ?>
            <div class="blogs_options" style="text-align: center;width: 120px">
              <?php echo $this->htmlLink('javascript:page_blog.edit('.$blog->getIdentity().');', '<div style="width: 50px; float: left;text-align: center;color: #25d3ff"><i class="hei hei-pencil-square-o"></i><span style="display: block;"> Edit</span></div>', array('title' => $this->translate('edit')) ); ?>
              <?php echo $this->htmlLink('javascript:page_blog.delete_blog('.$blog->getIdentity().');', '<div style="width: 50px; float: left;text-align: center;color: #FF2525"><i class="hei hei-trash-o"></i><span style="display: block;co" >Delete</span></div>', array('title' => $this->translate('delete'))); ?>
            </div>
          </div>
          <p class="blogs_browse_info_date">
            <?php echo $this->translate('Posted');?> <?php echo $this->timestamp($blog->creation_date); ?>
          </p>
          <p class="blogs_browse_info_blurb">
            <?php echo Engine_String::strip_tags(Engine_String::substr($blog->body, 0, 350)); if (Engine_String::strlen($blog->body)>349) echo $this->translate("..."); ?>
          </p>
        </div>
      </li>
    <?php endforeach; ?>
	</ul>
	
	<?php if( $this->blogs->count() > 1 ): ?>
		<?php echo $this->paginationControl($this->blogs, null, array("pagination.tpl","pageblog"), array(
      'page' => $this->subject
    ));?>
	<?php endif; ?>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has created a blog yet.');?>
      <?php if ($this->isAllowedPost):?>
        <?php echo $this->translate('Be the first to %1$spost%2$s one!', '<a href="javascript:page_blog.create()">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>