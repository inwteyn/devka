<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _composeHeevent.tpl 19.10.13 08:20 jungar $
 * @author     Jungar
 */
?>
<?php
$request = Zend_Controller_Front::getInstance()->getRequest();
$is_lineline = 0;
if ($request->getModuleName() == 'timeline' && $request->getControllerName() == 'profile' && $request->getActionName() == 'index') {
  $is_lineline = 1;
}
if($request->getModuleName()!='page'){
  return;
}

$this->headScript()
  ->appendFile('application/modules/Pageevent/externals/scripts/page_event_composer.js')
  ->appendFile('application/modules/Pageevent/externals/scripts/Pageevent.js');
$subject = Engine_Api::_()->core()->getSubject('page');
$this->content_info = $content_info = $subject->getContentInfo();
if (!empty($content_info['content']) ){
  $method = Zend_Controller_Front::getInstance()->getRequest()->getParam('method', false);
  $this->init_js_str = $this->getApi()->getInitJs($content_info, $method, $subject);
}else{
  $this->init_js_str = "";
}
$ipp = $request->getParam('itemCountPerPage', 10);
$view = $this;
?>

<script type="text/javascript">

  Wall.runonce.add(function () {

    try {

      var feed = Wall.feeds.get("<?php echo $this->feed_uid?>");

      feed.compose.addPlugin(new Wall.Composer.Plugin.Pageevent({
        title:'<?php echo $this->string()->escapeJavascript($this->translate('PAGE_Add Event')) ?>',
        lang:{
          'cancel':'<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>'
        },
        is_timeline: <?php echo $is_lineline; ?>
      }));

    } catch (e) {
    console.log(e);
    }
  });
  en4.core.runonce.add(function (){
    Pageevent.url.page = '<?php echo $subject->getHref(); ?>';
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
    Pageevent.page_id = <?php echo $subject->page_id?>;
    Pageevent.ipp = new Number('<?php echo $ipp;?>');
    Pageevent.init();
    <?php echo $this->init_js_str?>

  });
</script>
<div id="pageevent-composer-create-form" class="pageevent-create-form  pageevent-form global_form">
<?php
$form = new Pageevent_Form_Form($subject);
echo $form->render();
?>
</div>
<style type="text/css">
  .global_form textarea {
    font-size: 10pt;
    height: 10px !important;
    max-width: 400px;
    min-height: 60px !important;
    padding: 4px;
  }
  #event_photo-demo-list {
    height: 100px  !important;
  }
  .datepicker_vista{
    z-index: 50000;
  }
  #heevent-composer-create-form textarea {
    min-height: inherit;
    width: auto;
  }
  #heevent-composer-create-form #auth_view-wrapper{
    display: block;
  }

</style>

<div id="loader_pageevent" style="display: none"></div>
<div id="background_create_form"></div>



