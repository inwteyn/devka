<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: list.tpl 2011-09-01 13:17:53 kirill $
 * @author     Kirill
 */

?>


<?php if ($this->documents->getTotalItemCount() > 0): ?>
	<ul class="documents_browse">
	<?php foreach ($this->documents as $document): ?>

	<li>
    <div class="documents_browse_thumb">
        <i class="hei hei-file-text hei-4x"></i>
    </div>
		<div class="documents_browse_info">
			<p class="documents_browse_info_title">
				<?php echo $this->htmlLink($document->getHref(), $document->document_title, array('onclick'=>'page_document.view('.$document->getIdentity().'); return false;')); ?>
			</p>
			<p class="documents_browse_info_date">
				<?php echo $this->translate('pagedocument_Posted', $this->timestamp($document->creation_date)); ?><br>
        <?php $category = $document->getCategory(); ?>
        <?php if($category): ?>
          <?php echo $this->translate('pagedocument_Category', $category); ?>
        <?php else: ?>
          <?php echo $this->translate('pagedocument_Uncategorized'); ?>
        <?php endif;?>
			</p>
			<p class="documents_browse_info_blurb">
				<?php echo substr(strip_tags($document->document_description), 0, 500); if (strlen(strip_tags($document->document_description)) > 500) echo $this->translate("pagedocument_..."); ?>
			</p>
		</div>
    <div class="clr"></div>
	</li>
	<?php endforeach; ?>
	</ul>
	
	<?php if( $this->documents->count() > 1 ): ?>
		<?php echo $this->paginationControl($this->documents, null, array("pagination.tpl","pagedocument"), array(
      'page' => $this->pageObject
    ));?>
	<?php endif; ?>
<?php else: ?>

<div class="tip">
  <span>
    <?php echo $this->translate('pagedocument_No documents');?>
    <?php if ($this->isAllowedPost):?>
      <?php echo $this->translate('pagedocument_Post document', '<a href="javascript:page_document.create()">', '</a>'); ?>
    <?php endif; ?>
  </span>
</div>

<?php endif; ?>