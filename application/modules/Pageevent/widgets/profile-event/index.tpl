<?php

 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-07-02 17:53 michael $
 * @author     Michael
 */

$this->headScript()
    ->appendFile( $this->layout()->staticBaseUrl . 'application/modules/Pageevent/externals/scripts/Pageevent.js');

$this->headTranslate(array(
  'PAGEEVENT_DELETE_TITLE',
  'PAGEEVENT_DELETE_DESCRIPTION',
  'PAGEEVENT_CREATE_TITLE',
  'PAGEEVENT_CREATE_DESCRIPTION',
  'PAGEEVENT_EDIT_TITLE',
  'PAGEEVENT_EDIT_DESCRIPTION',
  'PAGEEVENT_MEMBERSBOX_ATTENDING',
  'PAGEEVENT_MEMBERSBOX_MAYBE_ATTENDING',
  'PAGEEVENT_MEMBERSBOX_NOT_ATTENDING',
  'PAGEEVENT_INVITE_DISABLED'
));

?>

<script type="text/javascript">
  en4.core.runonce.add(function (){
    Pageevent.url.page = '<?php echo $this->subject->getHref(); ?>';
    Pageevent.url.form = '<?php echo $this->url(array('action' => 'form'), 'page_event')?>';
    Pageevent.url.edit = '<?php echo $this->url(array('action' => 'edit'), 'page_event')?>';
    Pageevent.url.remove_photo = '<?php echo $this->url(array('action' => 'remove-photo'), 'page_event')?>';
    Pageevent.url.remove = '<?php echo $this->url(array('action' => 'remove'), 'page_event')?>';
    Pageevent.url.view = '<?php echo $this->url(array('action' => 'view'), 'page_event')?>';
    Pageevent.url.list = '<?php echo $this->url(array('action' => 'list'), 'page_event')?>';
    Pageevent.url.rsvp = '<?php echo $this->url(array('action' => 'rsvp'), 'page_event')?>';
    Pageevent.url.resource_approve = '<?php echo $this->url(array('action' => 'resource-approve'), 'page_event')?>';
    Pageevent.url.member_approve = '<?php echo $this->url(array('action' => 'member-approve'), 'page_event')?>';
    Pageevent.url.invite = '<?php echo $this->url(array('action' => 'invite'), 'page_event')?>';
    Pageevent.url.waiting = '<?php echo $this->url(array('action' => 'waiting'), 'page_event')?>';
    Pageevent.page_id = <?php echo $this->page_id?>;
    Pageevent.ipp = <?php echo $this->ipp;?>;
    Pageevent.init();
    <?php echo $this->init_js_str?>

  });

</script>

<div class="pageevent_navigation">
  <div class="navigation tabs">
    <?php
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->setPartial(array('_contentNavIcons.tpl', 'page'))
        ->render();
    ?>
  </div>
  <div class="pageevent_loader hidden" id="pageevent_loader">
    <?php echo $this->htmlImage( $this->baseUrl() . '/application/modules/Pageevent/externals/images/loader.gif'); ?>
  </div>
  <div style="clear:both;"></div>
</div>
<br />
<?php if(!empty($this->content_info['content']) && $this->content_info['content']=='page_event') { ?>
    <div class="pageevent" id="pageevent">
      <div class="tab_list hidden tab"><?php echo $this->render('list.tpl')?></div>
      <div class="tab_form hidden tab"><?php echo $this->form->render()?></div>
      <div class="tab_view tab">
        <?php
          if(!empty($this->content_info['content_id'])){
            $tmp = $this->action('view', 'index', 'pageevent', array('page_id'=>$this->subject->getIdentity(), 'id'=>$this->content_info['content_id']));
            echo $tmp;
            echo "<script type='text/javascript'>

              en4.core.runonce.add(function (){ Pageevent.view({$this->content_info['content_id']}); });</script>";
          }
        ?>
      </div>
      <div class="tab_message hidden tab"></div>
    </div>
<?php
} else {
?>
<div class="pageevent" id="pageevent">
  <div class="tab_list tab"><?php echo $this->render('list.tpl')?></div>
  <div class="tab_form hidden tab"><?php echo $this->form->render()?></div>
  <div class="tab_view hidden tab"></div>
  <div class="tab_message hidden tab"></div>
</div>
<?php } ?>