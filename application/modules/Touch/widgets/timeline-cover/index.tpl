<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

?>
<?php
$this->headScript()
  ->appendFile($this->baseUrl() . '/application/modules/Timeline/externals/scripts/cover.js');
?>

<script type="text/javascript">
  document.tl_cover = new TimelineCover();
</script>

<?php if($this->canEdit): ?>
<script type="text/javascript">
  document.tl_cover.setOptions({
    'element_id':'cover-photo',
    'edit_buttons':'tl-cover-edit',
    'loader_id':'tl-cover-loader',
    'is_allowed':true,
    'cover_url':'<?php echo $this->url(array('action' => 'get', 'id' => $this->subject()->getIdentity()), 'timeline_photo', true); ?>',
    'position_url':'<?php echo $this->url(array('action' => 'position', 'id' => $this->subject()->getIdentity()), 'timeline_photo', true); ?>'
  });

  document.tl_cover.position.top = <?php echo $this->position['top']; ?>;
  document.tl_cover.position.left = <?php echo $this->position['left']; ?>;

  en4.core.runonce.add(function () {
    document.tl_cover.init();
    document.tl_cover.options.cover_width = document.tl_cover.get().getParent().getWidth();
  });
</script>
<?php endif; ?>
<script type="text/javascript">
  var tlci = window.setInterval(function(){
    if($('tl-cover')){
      window.clearInterval(tlci);
      var tlca = $('tl-cover').getElement('a');
      var tlc = tlca.getElement('img');
      var tlc_y_offset;
      var tlc_x_offset;


      if(tlc){
        tlc_y_offset = parseInt(tlc.getStyle('top'));
        tlc_x_offset = parseInt(tlc.getStyle('left'));
      }

      var apps = document.body.getElement('.applications');
      window.addEvent('resize', function(e){
        if(tlc){
          var width = parseInt(tlca.getStyle('width'));
          var k = width/982;
          var height =   315*k;
          tlca.setStyle('height', height+'px');
          tlc.setStyle('top',tlc_y_offset*k+'px');
          tlc.setStyle('left', tlc_x_offset*k+'px');
        }
        if($('tl-born')){
          var tlba = $('tl-born').getElement('a');
          var tlb = tlba.getElement('img');
          var tlb_y_offset;
          var tlb_x_offset;

          if(tlb){
            tlb_y_offset = parseInt(tlb.getStyle('top'));
            tlb_x_offset = parseInt(tlb.getStyle('left'));
            var width = parseInt(tlba.getStyle('width'));
            var k = width/838;
            var height =   403*k;
            tlba.setStyle('height', height+'px');
            tlb.setStyle('top',tlb_y_offset*k+'px');
            tlb.setStyle('left', tlb_x_offset*k+'px');
          }
        }
      });
//      if(Touch.isIPhone())
      new Drag.Scroll(document.body.getElement('.applications'), {
          axis: {x: true, y: true}
      });
//      if(Touch.isAndroid())
//        new MTScrollView(apps, {
//          axis:['x']
//        });
      window.fireEvent('resize');
    }
  }, 100)
</script>
<div>
  <div class="tl-block cover <?php if (!$this->coverExists): ?> no-cover <?php endif; ?>">

    <div id='tl-cover'>
      <a href="javascript:void(0);"
        <?php if ($this->coverExists && Engine_Api::_()->getApi('settings', 'core')->getSetting('wall.content.slideshow', true) && $this->albumPhoto): ?>
         onclick="tl_cover.slideShow('<?php echo $this->albumPhoto->getPhotoUrl(); ?>', '<?php echo $this->albumPhoto->getGuid(); ?>', this)"
        <?php endif; ?>
         style="height: <?php echo $this->coverHeight;?>px">
        <?php
          if ($this->coverExists)
            echo $this->subject()->getTimelinePhoto();
          else
            echo $this->translate('TOUCH_tl_no_cover_photo');
        ?>
      </a>
    </div>

    <?php if ($this->canEdit): ?>

    <div id='tl-cover-edit' class="tl-options cover-edit">
      <div>
        <div></div>
        <div>
          <div></div>
        </div>
      </div>
      <ul class="tl-in-block">
        <li class="save">
          <?php echo $this->htmlLink(
          'javascript://',
          $this->translate('TIMELINE_Save Positions'),
          array('class' => 'save-positions hidden')); ?>
        </li>
        <li class="more">
          <?php echo $this->htmlLink('javascript://', $this->translate($this->label), array(
          'class' => 'cover-change buttonlink'
        )); ?>

          <ul class="cover-options tl-in-block visiblity-hidden">

            <?php if ($this->isAlbumEnabled): ?>
            <li><?php echo $this->htmlLink(array(
                'route' => 'timeline_photo',
                'id' => $this->subject()->getIdentity(),
                'reset' => true
              ),
              $this->translate('TIMELINE_Choose from Photos...'),
              array(
                'class' => 'cover-albums smoothbox',
              )); ?>
            </li>
            <?php endif; ?>

            <li><?php echo $this->htmlLink(array(
              'route' => 'timeline_photo',
              'action' => 'upload',
              'id' => $this->subject()->getIdentity(),
            ), $this->translate('TIMELINE_Upload Photo...'), array(
              'class' => 'cover-upload smoothbox')); ?>
            </li>

            <li><?php echo $this->htmlLink(
              'javascript:document.tl_cover.reposition.start()',
              $this->translate('TIMELINE_Reposition...'),
              array('class' => 'cover-reposition')); ?>
            </li>

            <li><?php echo $this->htmlLink(array(
                'route' => 'timeline_photo',
                'action' => 'remove',
                'id' => $this->subject()->getIdentity(),
              ),
              $this->translate('TIMELINE_Remove...'), array(
                'class' => 'cover-remove smoothbox')); ?>
            </li>
          </ul>

        </li>
      </ul>
    </div>

    <?php endif; ?>

  </div>
</div>