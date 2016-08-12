  <?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: list_edit.tpl 2011-09-01 13:17:53 kirill $
 * @author     Kirill
 */

?>

<?php if ($this->documents->getTotalItemCount() > 0): ?>
	<ul class="documents_browse">

    <?php foreach ($this->documents as $document): ?>
    <li>
      <div class="documents_browse_thumb">
        <?php
          if( is_null($document->thumbnail)){
            if($document->doc_id){
                $settings = $this->scribd->getSettings($document->doc_id, $this->viewer->getIdentity());
                $document->thumbnail = $settings['thumbnail_url'];
                $document->save();
            }
          }
        ?>
        <img src="<?php echo $document->thumbnail; ?>">
      </div>
      <div class="documents_browse_info">
        <div class="documents_browse_info_title">
          <?php echo $this->htmlLink($document->getHref(), $document->document_title, array('onclick'=>'page_document.view('.$document->getIdentity().'); return false;')); ?>
          <div class="documents_options" style="float: right;">
            <?php echo $this->htmlLink('javascript:page_document.edit('.$document->getIdentity().');', $this->htmlImage($this->baseUrl() . '/application/modules/Pagedocument/externals/images/edit16.png', '', array('border' => 0)), array('title' => $this->translate('pagedocument_edit')) ); ?>

            <?php echo $this->htmlLink('javascript:page_document.delete_document('.$document->getIdentity().');', $this->htmlImage($this->baseUrl() . '/application/modules/Pagedocument/externals/images/delete16.png', '', array('border' => 0)), array('title' => $this->translate('pagedocument_delete')));?>

          </div>
        </div>
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
          <?php echo substr(strip_tags($document->document_description), 0, 500); if (strlen($document->document_description)>349) echo $this->translate("pagedocument_..."); ?>
        </p>
      </div>
      <div class="clr"></div>
      <?php /*if($document->status != 'DONE'){ $document->checkState(); */?><!--
         <?php /*if($document->status=='PROCESSING'){ */?>
            <div class="pagedocument-conversion"><?php /*echo $this->translate('pagedocument_Document in processing'); */?></div>
         <?php /*} elseif($document->status == 'ERROR'){ */?>
            <div class="pagedocument-error"><?php /*echo $this->translate('pagedocument_Document error');*/?></div>
        <?php /*} */?>
      --><?php /*}*/?>
    </li>
    <?php endforeach; ?>
	</ul>

	<?php echo $this->paginationControl($this->documents, null, array("pagination.tpl","pagedocument"), array('page' => $this->pageObject)); ?>

<?php else: ?>
<div class="tip">
  <span>
    <?php echo $this->translate('pagedocument_No user documents');?>
    <?php if ($this->isAllowedPost):?>
      <?php echo $this->translate('pagedocument_Post document', '<a href="javascript:page_document.create()">', '</a>'); ?>
    <?php endif; ?>
  </span>
</div>
<?php endif; ?>
