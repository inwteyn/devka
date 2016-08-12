<?php

$this->headScript()
    ->appendFile( $this->layout()->staticBaseUrl . 'application/modules/Pagediscussion/externals/scripts/Pagediscussion.js');

$this->headTranslate(array(
  'PAGEDISCUSSION_DELETETOPIC_TITLE',
  'PAGEDISCUSSION_DELETETOPIC_DESCRIPTION',
  'PAGEDISCUSSION_DELETEPOST_TITLE',
  'PAGEDISCUSSION_DELETEPOST_DESCRIPTION'
));

?>

<script type="text/javascript">

en4.core.runonce.add(function () {
  Pagediscussion.url.create = '<?php echo $this->url(array('action' => 'create'), 'page_discussion')?>';
  Pagediscussion.url.page = '<?php echo $this->subject->getHref(); ?>';
  Pagediscussion.url.post = '<?php echo $this->url(array('action' => 'post'), 'page_discussion')?>';
  Pagediscussion.url.rename = '<?php echo $this->url(array('action' => 'rename'), 'page_discussion')?>';
  Pagediscussion.url.edit = '<?php echo $this->url(array('action' => 'edit'), 'page_discussion')?>';
  Pagediscussion.url.discussion = '<?php echo $this->url(array('action' => 'discussion'), 'page_discussion')?>';
  Pagediscussion.url.list = '<?php echo $this->url(array('action' => 'index'), 'page_discussion')?>';
  Pagediscussion.url.topic = '<?php echo $this->url(array('action' => 'topic'), 'page_discussion')?>';
  Pagediscussion.page_id = <?php echo $this->page_id?>;
  Pagediscussion.topic_list = <?php echo $this->jsonInline($this->topic_list)?>;
  Pagediscussion.ipp = <?php echo $this->ipp;?>;
  Pagediscussion.init();
  <?php echo $this->init_js_str?>
});

</script>

<div class="pagediscussion_loader hidden" id="pagediscussion_loader">
  <?php echo $this->htmlImage( $this->baseUrl() . '/application/modules/Pagediscussion/externals/images/loader.gif'); ?>
</div>
<div class="clr"></div>

<div class="pagediscussion" id="pagediscussion">

  <div class="tab_list tab">
    <?php
    if($this->content_info['content'] == 'discussion') {
      if(!empty($this->content_info['content'])){
        $tmp = $this->action('topic', 'index', 'pagediscussion', array('page_id'=>$this->subject->getIdentity(), 'topic_id'=>$this->content_info['content_id']));
        echo $tmp;
      }
    } else {
      echo $this->render('list.tpl');
    }
    ?>
  </div>
  <div class="tab_create tab hidden">
    <?php echo $this->formCreate->render();?>
  </div>
  <div class="tab_post tab hidden">
    <?php echo $this->formPost->render();?>
  </div>
  <div class="tab_rename tab hidden">
    <?php echo $this->formRename->render();?>
  </div>
  <div class="tab_edit tab hidden">
    <?php echo $this->formEdit->render();?>
  </div>

  <div class="tab_message tab hidden"></div>

  <div class="tab_topic tab hidden"></div>

</div>
