<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.php 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */
?>

<script type="text/javascript">
  en4.core.runonce.add(function(){

  <?php if( !$this->renderOne ): ?>
    var anchor = $('profile_pageevents').getParent();
    $('profile_pageevents_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('profile_pageevents_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('profile_pageevents_previous').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
        }
      }), {
        'element' : anchor
      })
    });

    $('profile_pageevents_next').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
        }
      }), {
        'element' : anchor
      })
    });
    <?php endif; ?>
  });
</script>

<ul id="profile_pageevents" class="pageevents_profile_tab">
  <?php foreach( $this->paginator as $item ): ?>
  <?php
    if( $item['type'] == 'page')
      $event = Engine_Api::_()->getItem('pageevent', $item['event_id']);
    else
      $event = Engine_Api::_()->getItem('event', $item['event_id']);
  ?>
  <li>
    <div class="pageevents_profile_tab_photo">
      <?php echo $this->htmlLink($event, $this->itemPhoto($event, 'thumb.normal')) ?>
    </div>
    <div class="pageevents_profile_tab_info">
      <div class="pageevents_profile_tab_title">
        <?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?>
      </div>
      <div class="pageevents_members">
        <?php echo $this->locale()->toDateTime($event->starttime) ?>
      </div>
      <div class="pageevents_profile_tab_members">
        <?php if($item['type'] == 'page') : ?>
          <?php echo $this->translate('On page'); ?>
          <?php echo $this->htmlLink($event->getPage()->getHref(), $event->getPage()->getTitle());?>
          -
        <?php endif; ?>
        <?php echo $this->translate(array('%s guest', '%s guests', $event->member_count),$this->locale()->toNumber($event->member_count)) ?>
      </div>
      <div class="pageevents_profile_tab_desc">
        <?php echo $event->getDescription() ?>
      </div>
    </div>
  </li>
  <?php endforeach; ?>
</ul>

<div>
  <div id="profile_pageevents_previous" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
    'onclick' => '',
    'class' => 'buttonlink icon_previous'
  )); ?>
  </div>
  <div id="profile_pageevents_next" class="paginator_next">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
    'onclick' => '',
    'class' => 'buttonlink_right icon_next'
  )); ?>
  </div>
</div>