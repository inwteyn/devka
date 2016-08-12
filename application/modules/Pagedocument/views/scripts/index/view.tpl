<div class='pagedocument' id="page_document_container">
  <h3>
    <?php echo $this->document->getTitle(); ?>
  </h3>

  <?php //if (!$this->isAllowedPost): ?>
<div class="backlink_wrapper">
  <a class="backlink" href="javascript:page_document.list()"><?php echo $this->translate('Back To Documents'); ?></a>
</div>
<?php //endif; ?>

  <div class="pagedocument-info">
    <?php echo $this->translate("pagedocument_Posted", $this->timestamp($this->document->creation_date)); ?><br />
    <?php if($this->document->getCategory()): ?>
      <?php echo $this->translate('pagedocument_Category', $this->document->getCategory()); ?>
    <?php else: ?>
      <?php echo $this->translate('pagedocument_Uncategorized'); ?>
    <?php endif;?><br />
    <?php echo $this->translate("pagedocument_Views", $this->document->view_count); ?>
  </div>

  <div class="pagedocument-description">
      <?php echo $this->document->document_description; ?>
  </div>


  <div class="pagedocument-options">
    <?php
      if($this->document->download_allow != '' && $this->document->download_allow != 'view-only'){
        $link = $this->scribd->getDonloadLink($this->document->doc_id, 'original', $this->viewer->getIdentity());
        if($link && $link['download_link']){
          echo $this->htmlLink($link['download_link'], $this->translate("Download"), array('target' => '_blank'));
        }
      }
    ?>
  </div>


    <div id='embedded_doc'>
        <?php
            $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https://" : "http://";
            $url = ($protocol.$_SERVER['HTTP_HOST']);
        if($this->document->storage_path){
            $viewer = Engine_Api::_()->user()->getViewer();

        ?>
          <?php
          
            if($viewer->isAdmin()&& $this->document->file_link_google!=""){?>
                <iframe src="https://docs.google.com/document/d/<?php echo $this->document->file_link_google;?>/edit?usp=sharing" width="930" height="500" frameborder="0">
                </iframe>
           <?php }else{ ?>



        <iframe src="http://docs.google.com/viewer?url=<?php echo $url.$this->document->storage_path;?>&embedded=true" width="930" height="500" frameborder="0">
        </iframe>
        <?php }} ?>
    </div>


</div>



<?php if (Engine_Api::_()->getDbTable('modules' ,'hecore')->isModuleEnabled('wall')): ?>
  <?php echo $this->wallComments($this->document, $this->viewer()); ?>
<?php else: ?>
<div class="comments" id="pagedocument_comments"></div>
<?php endif;?>