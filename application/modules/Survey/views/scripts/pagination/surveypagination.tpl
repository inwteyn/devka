<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright 2006-2010 Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: surveypagination.tpl ermek $
 * @author     Ermek
 */
?>

<?php if ($this->pageCount > 1): ?>
  <div class="paginationControl">

    <?php /* Previous page link */ ?>
    <?php if (isset($this->previous)): ?>
      <a href="javascript:void(0)" onclick="javascript:pageAction(<?php echo $this->previous;?>)"><?php echo $this->translate('survey_&#171; Previous');?></a>
      <?php if (isset($this->previous)): ?>
      &nbsp;|
      <?php endif; ?>
    <?php endif; ?>

    <?php foreach ($this->pagesInRange as $page): ?>
      <?php if ($page != $this->current): ?>
        <a href="javascript:void(0)" onclick="javascript:pageAction(<?php echo $page;?>)"><?php echo $page;?></a> |
      <?php else: ?>
        <?php echo $page; ?> |
      <?php endif; ?>
    <?php endforeach; ?>

    <?php /* Next page link */ ?>
    <?php if (isset($this->next)): ?>
        <a href="javascript:void(0)" onclick="javascript:pageAction(<?php echo $this->next;?>)"><?php echo $this->translate('survey_Next &#187;');?></a>
    <?php endif; ?>

  </div>
<?php endif; ?>