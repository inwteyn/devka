<?php
$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'externals/tinymce/tiny_mce.js')
  ->appendFile($this->baseUrl() . '/application/modules/Offers/externals/scripts/offers.js');

$subject = Engine_Api::_()->core()->getSubject();
$form_id = $this->form->getAttrib('id');
?>

<script type="text/javascript">
  Offers.url.list = "<?php echo $this->url(array('action' => 'index'), 'offers_page'); ?>";
  Offers.url.page = "<?php echo $subject->getHref(); ?>";
  Offers.url.create = "<?php echo $this->url(array('action' => 'create'), 'offers_page'); ?>";
  Offers.url.view = "<?php echo $this->url(array('action' => 'view'), 'offers_page'); ?>";
  Offers.url.my_offers = "<?php echo $this->url(array('action' => 'mine'), 'offers_page'); ?>";
  Offers.url.form = "<?php echo $this->url(array('action' => 'create'), 'offers_page'); ?>";
  Offers.url.manage_photos = "<?php echo $this->url(array('action' => 'manage-photos'), 'offers_page'); ?>";
  Offers.url.edit_photos = "<?php echo $this->url(array('action' => 'edit-photos'), 'offers_page');  ?>";
  Offers.url.contacts = "<?php echo $this->url(array('action' => 'set-contacts-offer'), 'offers_page') ?>";
  Offers.list_param.page_id = "<?php echo $subject->getIdentity(); ?>";
  Offers.container_id = 'offers_container';
  Offers.allowed_post = <?php echo (int) $this->isAllowedPost; ?>;

  en4.core.runonce.add(function(){
    Offers.init();
    Offers.formFilter($('oftype_free'), 'free');
  });
</script>

<div id="offers_navigation">
  <div class="navigation tabs">
    <?php
    echo $this->navigation()->menu()->setContainer($this->navigation)->setPartial(array('_contentNavIcons.tpl', 'page'))
       ->render();
    ?>
    <div class="offer_loader hidden" id="offer_loader">
      <?php echo $this->htmlImage($this->baseUrl().'/application/modules/Offers/externals/images/loader.gif'); ?>
    </div>
  </div>
  <div class="clr"></div>
</div>
<div id="offers" class="offers">
  <div class="tab_list tab">
    <?php echo $this->render('list.tpl'); ?>
  </div>
  <div class="tab_form hidden tab" id="create_offer">
    <?php echo $this->render('form.tpl')?>
  </div>
  <div class="tab_contacts hidden tab" id="contacts_offer">
    <?php echo $this->offerContactForm; ?>
  </div>
  <div class="tab_message hidden tab"></div>
</div>