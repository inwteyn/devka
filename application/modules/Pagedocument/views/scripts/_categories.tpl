<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _editMenu.tpl 2011-09-21 17:53 mirlan $
 * @author     Mirlan
 */
?>
<?php if($this->categories->getTotalItemCount() > 0) : ?>
<div class="pagedocument-categories-widget">
    <a href="javascript:void(0);" onclick="page_document.set_category(-1, '<?php echo $this->view_type; ?>');" <?php if($this->active_category == -1) echo 'class="active"'?>  >
        <?php echo $this->translate('pagedocument_All categories'); ?>
    </a>
    <?php $cnt = 0;
        foreach($this->categories as $category) : ?>
                <?php if($cnt >= 3) : ?> <br>  <?php endif; ?>
                    <a href="javascript:void(0);" onclick="page_document.set_category('<?php echo $category->category_id; ?>', '<?php echo $this->view_type; ?>');"  <?php if($this->active_category == $category->category_id) echo 'class="active"'?> >
                        <?php echo $category->getTitle();
                            if($this->view_type == 'mine')
                                $docs = $category->getDocumentsCount($this->viewer->getIdentity(),  $this->page_id);
                            else
                                $docs = $category->getDocumentsCount(null,  $this->page_id);
                            if($docs>0) echo '(' . $docs . ')';
                        ?>
                    </a>
                <?php $cnt++; ?>
    <?php endforeach; ?>
    <?php if($this->uncategorized != 0) :?>
        <a href="javascript:void(0);" onclick="page_document.set_category(0, '<?php echo $this->view_type; ?>');"  <?php if($this->active_category == 0) echo 'class="active"'?> >
            <?php echo $this->translate('pagedocument_Uncategorized') . '(' . $this->uncategorized . ')'; ?>
        </a>
    <?php endif; ?>

        </div>
<?php endif; ?>