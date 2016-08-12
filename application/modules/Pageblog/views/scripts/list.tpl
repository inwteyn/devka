<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: list.tpl 2010-08-31 17:53 idris $
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
          <p class="blogs_browse_info_title">
            <?php echo $this->htmlLink($blog->getHref(),
                                       $blog->getTitle(),
                                       array('onclick' => "page_blog.view({$blog->getIdentity()}); return false;"));
            ?>
          </p>
          <p class="blogs_browse_info_date">
            <?php echo $this->translate('Posted');?> <?php echo $this->timestamp($blog->creation_date); ?>
          </p>
          <p class="blogs_browse_info_blurb">
            <?php echo Engine_String::substr(Engine_String::strip_tags($blog->body), 0, 500); if (Engine_String::strlen(Engine_String::strip_tags($blog->body)) > 500) echo $this->translate("..."); ?>
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