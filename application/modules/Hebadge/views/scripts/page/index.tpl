<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 02.04.12 09:12 michael $
 * @author     Michael
 */
?>


<div class="layout_hebadge_page_badges">

  <?php

    $this->headTranslate(array(
      'HEBADGE_CONFIRM_PAGE_REQUEST_SEND_TITLE',
      'HEBADGE_CONFIRM_PAGE_REQUEST_SEND_DESCRIPTION',
      'HEBADGE_CONFIRM_PAGE_REQUEST_SEND_MESSAGE',
      'HEBADGE_CONFIRM_PAGE_REQUEST_REJECT_TITLE',
      'HEBADGE_CONFIRM_PAGE_REQUEST_REJECT_DESCRIPTION'
    ));

  ?>

  <script type="text/javascript">
    en4.core.runonce.add(function (){
      $$('.hebadge_widget_browse .item_photo a').each(function (item){
        Hebadge.elementClass(Hebadge.Tips, item, {'title': item.getParent('li').getElement('.item_info').get('html'), 'top': false, 'left': true});
      });
    });
  </script>


  <div class="hebadge_widget_description">
    <?php echo $this->translate('HEBADGE_WIDGET_DESCRIPTION_' . strtoupper($this->simple_name));?>
  </div>

  <script type="text/javascript">

    function hebadgePageRequestSend(element, badge_id)
    {
      var title = en4.core.language.translate('HEBADGE_CONFIRM_PAGE_REQUEST_SEND_TITLE');
      var description = en4.core.language.translate('HEBADGE_CONFIRM_PAGE_REQUEST_SEND_DESCRIPTION');

      description += '<div>' + en4.core.language.translate('HEBADGE_CONFIRM_PAGE_REQUEST_SEND_MESSAGE') + '</div>';
      description += '<textarea rows="1" cols="1" class="hebadge_page_request_textarea"></textarea>';

      var message = '';

      var callback = function (){

        var data = {
          'message': message
        };

        Hebadge.request(en4.core.baseUrl + 'hebadge/page/request/format/json/page_id/<?php echo $this->subject()->getIdentity()?>/pagebadge_id/'+badge_id, data);

        $(element)
            .getParent('div')
            .getElements('.hebadge-button-page_request_send, .hebadge-button-page_request_reject')
            .setStyle('display', 'none');

        $(element)
            .getParent('div')
            .getElements('.hebadge-button-page_request_cancel')
            .setStyle('display', 'inline-block');

      };



      he_show_confirm(title, description, function(){});

      $('TB_ajaxContent').getElement('.he_confirm_tools .confirm_btn').removeEvents().addEvent('click', function() {
        message = $('TB_ajaxContent').getElement('.hebadge_page_request_textarea').value;
        Smoothbox.close();
        callback();
      });



    }

    function hebadgePageRequestCancel(element, badge_id)
    {
      Hebadge.request(en4.core.baseUrl + 'hebadge/page/request-cancel/format/json/page_id/<?php echo $this->subject()->getIdentity()?>/pagebadge_id/'+badge_id);

      $(element)
          .getParent('div')
          .getElements('.hebadge-button-page_request_cancel, .hebadge-button-page_request_reject')
          .setStyle('display', 'none');

      $(element)
          .getParent('div')
          .getElements('.hebadge-button-page_request_send')
          .setStyle('display', 'inline-block');

    }

    function hebadgePageRequestReject(element, badge_id)
    {
      var title = en4.core.language.translate('HEBADGE_CONFIRM_PAGE_REQUEST_REJECT_TITLE');
      var description = en4.core.language.translate('HEBADGE_CONFIRM_PAGE_REQUEST_REJECT_DESCRIPTION');

      he_show_confirm(title, description, function (){

        Hebadge.request(en4.core.baseUrl + 'hebadge/page/request-reject/format/json/page_id/<?php echo $this->subject()->getIdentity()?>/pagebadge_id/'+badge_id);

        $(element)
            .getParent('div')
            .getElements('.hebadge-button-page_request_cancel, .hebadge-button-page_request_reject')
            .setStyle('display', 'none');

        $(element)
            .getParent('div')
            .getElements('.hebadge-button-page_request_send')
            .setStyle('display', 'inline-block');
      });



    }

  </script>

  <?php if ($this->paginator->getTotalItemCount()):?>
    <ul class="hebadge_widget_browse hebadge_widget_browse_page">
      <?php foreach ($this->paginator as $badge):?>


      <?php
      if (!empty($this->members[$badge->getIdentity()]) && $this->members[$badge->getIdentity()]->approved){
        $active = 'request_reject';
      } elseif (!empty($this->members[$badge->getIdentity()]) && !$this->members[$badge->getIdentity()]->approved) {
        $active = 'request_cancel';
      } else {
        $active = 'request_send';
      }
      ?>


      <li class="hebadge_badge_<?php echo $active?>">
          <div class="item_photo">
            <a href="<?php echo $badge->getHref()?>">
              <?php echo $this->itemPhoto($badge, 'thumb.profile');?>
            </a>
          </div>
          <div class="item_body">
            <div class="item_title"><a href="<?php echo $badge->getHref()?>"><?php echo $badge->getTitle();?></a></div>
            <div class="item_description">
              <a href="<?php echo $badge->getHref()?>">
                <?php echo $this->translate(array('%1$s page', '%1$s pages', $badge->member_count), $badge->member_count);?>
              </a>
              <div style="margin:10px 0;text-align: center;">


                <a href="javascript:void(0)" onclick="hebadgePageRequestSend(this, <?php echo $badge->getIdentity();?>)" class="hebadge-button hebadge-button-page_request_send" <?php if ($active != 'request_send'):?>style="display:none;"<?php endif;?>><?php echo $this->translate('HEBADGE_PAGE_REQUEST_SEND')?></a>
                <a href="javascript:void(0)" onclick="hebadgePageRequestCancel(this, <?php echo $badge->getIdentity();?>)" class="hebadge-button hebadge-button-page_request_cancel" <?php if ($active != 'request_cancel'):?>style="display:none;"<?php endif;?>><?php echo $this->translate('HEBADGE_PAGE_REQUEST_CANCEL')?></a>
                <a href="javascript:void(0)" onclick="hebadgePageRequestReject(this, <?php echo $badge->getIdentity();?>)" class="hebadge-button hebadge-button-page_request_reject" <?php if ($active != 'request_reject'):?>style="display:none;"<?php endif;?>><?php echo $this->translate('HEBADGE_PAGE_REQUEST_REJECT')?></a>


              </div>
              <?php if (!empty($this->members[$badge->getIdentity()]) && $this->members[$badge->getIdentity()]->approved):?>
                <div class="hebadge_complete"><span><?php echo $this->translate('HEBADGE_PAGE_COMPLETE');?></span></div>
              <?php endif;?>
            </div>
            <div style="display: none;" class="item_info">
              <div class="item_title"><?php echo $badge->getTitle()?></div>
              <div class="item_description"><?php echo $badge->getDescription()?></div>
            </div>
          </div>
        </li>
      <?php endforeach;?>
    </ul>

  <?php else:?>

    <?php if (!empty($this->params) && !empty($this->params['text'])):?>
      <div class="tip"><span><?php echo $this->translate('HEBADGE_WIDGET_NOITEMS_SEARCH_' . strtoupper($this->simple_name) );?></span></div>
    <?php else :?>
      <div class="tip"><span><?php echo $this->translate('HEBADGE_WIDGET_NOITEMS_' . strtoupper($this->simple_name) );?></span></div>
    <?php endif;?>

  <?php endif;?>



  <?php if ($this->paginator->count() > 1): ?>
    <?php echo $this->paginationControl($this->paginator, null, array("pagination.tpl","hebadge"), array(
      'ajax_url' => $this->url(array_merge(array('module' => 'hebadge', 'controller' => 'page', 'action' => 'index', 'page_id' => $this->subject()->getIdentity()),$this->params), 'default', true),
      'ajax_class' => 'layout_' . $this->simple_name,
      'params' => $this->params
    ))?>
    <br />
<?php endif?>

</div>