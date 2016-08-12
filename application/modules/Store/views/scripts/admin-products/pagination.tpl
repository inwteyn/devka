<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: pagination.tpl  30.01.12 19:38 TeaJay $
 * @author     Taalay
 */
?>

<?php if ($this->pageCount > 1): ?>
  <ul class="paginationControl">
    <?php if (isset($this->previous)): ?>
      <li>
        <a href="javascript:void(0)" onclick="changePage(<?php echo $this->previous;?>, '<?php echo $this->identity?>', '<?php echo $this->class?>')"><?php echo $this->translate('&#171; Previous');?></a>
      </li>
    <?php endif; ?>

    <?php foreach ($this->pagesInRange as $page): ?>
      <li class="<?php if ($page == $this->current): ?>selected<?php endif; ?>" >
        <a onclick="changePage(<?php echo $page;?>, '<?php echo $this->identity?>', '<?php echo $this->class?>')" href="javascript:void(0)"><?php echo $this->locale()->toNumber($page); ?></a>
      </li>
    <?php endforeach; ?>

    <?php if (isset($this->next)): ?>
      <li>
        <a href="javascript:void(0)" onclick="changePage(<?php echo $this->next;?>, '<?php echo $this->identity?>', '<?php echo $this->class?>')"><?php echo $this->translate('Next &#187;');?></a>
      </li>
    <?php endif; ?>
  </ul>
<?php endif; ?>