<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: pagination.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<?php if ($this->pageCount > 1): ?>
  <?php
    if (Engine_Api::_()->core()->hasSubject('page')) {
      $subject = Engine_Api::_()->core()->getSubject('page');
      $href = $subject->getHref() . '/content/blog_page/content_id/';
    } else {
      $href = $this->page->getHref() . '/content/blog_page/content_id/';
    }
  ?>
  <ul class="paginationControl">
    <?php if (isset($this->previous)): ?>
      <li>
        <a href="<?php echo $href . $this->previous;?>" onclick="page_blog.set_page(<?php echo $this->previous;?>);return false;"><?php echo $this->translate('&#171; Previous');?></a>
      </li>
    <?php endif; ?>
		
    <?php foreach ($this->pagesInRange as $page): ?>
      <li class="<?php if ($page == $this->current): ?>selected<?php endif; ?>" >
        <a onclick="page_blog.set_page(<?php echo $page;?>);return false;" href="<?php echo $href . $page; ?>"><?php echo $page; ?></a>
      </li>
    <?php endforeach; ?>

    <?php if (isset($this->next)): ?>
    	<li>
        <a href="<?php echo $href . $this->next;?>" onclick="page_blog.set_page(<?php echo $this->next;?>);return false;"><?php echo $this->translate('Next &#187;');?></a>
      </li>
    <?php endif; ?>
  </ul>
<?php endif; ?>