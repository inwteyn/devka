<?php
/**
 * SocialEngine
 *
 * @category Application_Extensions
 * @package Timeline
 * @copyright Copyright Hire-Experts LLC
 * @license http://www.hire-experts.com
 * @version Id: index.tpl 2/11/12 3:25 PM mt.uulu $
 * @author Mirlan
 */
?>

<?php
$this->headScript()
  ->appendFile($this->baseUrl() . '/application/modules/Timeline/externals/scripts/timeline.js')
  ->appendFile($this->baseUrl() . '/application/modules/Timeline/externals/scripts/born.js')
  ;
?>

<script type="text/javascript">
  var timeline = new TimeLine();
  window.timeline_object = timeline;
  window.addEvent('domready', function() {
      var scroller = $('tl-dates');
      if (scroller) {
          var left = $('global_content').getSize().x +(window.getSize().x - $('global_content').getSize().x) /2 + 10,
              top_ab = $('global_header').getSize().y + 15;
          scroller.setStyles({
              'position': 'fixed',
              'top': top_ab + 'px'
          });
      }
  });



</script>

<div class="tl-right" >
  <ul id='tl-dates' style="position:fixed; ">
    <?php echo $this->partial('application/modules/Timeline/views/scripts/_timelineDates.tpl', null, array(
    'dates' => $this->dates,
    'subject_uid' => $this->subject_uid,
  )); ?>
  </ul>
</div>

<div class="tl-content <?php if( !$this->subject()->isSelf($this->viewer())): ?>none-active<?php endif; ?>" >

<!--  <div id="timeline">
    <div class="line"></div>
    <div class="plus">
      <div>
        <div class="ver"></div>
        <div class="hor"></div>
      </div>
    </div>
  </div>-->
  <div id="tl-feed">
   <!-- <div class="loader">
      <a class="buttonlink icon_loading"><?php /*echo $this->translate('Loading'); */?></a>
    </div>
-->
    <?php echo $this->content()->renderWidget('timeline.feed'); ?>
  </div>

  <?php if($this->subject()->isSelf($this->viewer())): ?>
  <div id="tl-composer" class="click-listener bound-timeline" style="display: none">
    <?php echo $this->content()->renderWidget('timeline.feed', array('composerOnly' => true)); ?>
  </div>
  <?php endif; ?>
</div>