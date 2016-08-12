<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: tag.tpl 2011-11-17 17:53 ermek $
 * @author     Ermek
 */
?>
<?php
  $this->headScript()
//    ->appendFile('http://maps.googleapis.com/maps/api/js?sensor=true&libraries=places')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Touch/modules/Checkin/externals/scripts/composer_checkin.js');

  $this->headTranslate(array('CHECKIN_Share location', 'CHECKIN_Where are you?', 'CHECKIN_%s were here'));
?>

<?php
$hide_on_page_profile = true; //todo get from settings
if (!$hide_on_page_profile || !$this->subject() || $this->subject()->getType() != 'page') :
?>


<script type="text/javascript">
  Wall.runonce.add(function (){
    var feed = Wall.feeds.get("<?php echo $this->feed_uid?>");
    var checkin = new Wall.Composer.Plugin.Checkin({});
    feed.compose.addPlugin(checkin);
    checkin.suggestUrl = <?php echo $this->jsonInline($this->url(array(''), 'default')); ?>;
  });

</script>
<span class="touch_lang_4_js TOUCH_Select"><?php echo $this->translate('TOUCH_Select') ?></span>

<div class="checkinWallShareLocation touch_tab_group display_none">
  <div class="share_loc_btn touch_tab_right touch_tab_dark">
    <a class="checkinShareLoc " href="javascript://" rev="checkin"></a>
  </div>
  <div class="touch_tab touch_tab_middle display_none">
  <a class="checkinLocationInfo " href="javascript://"><?php echo $this->translate('CHECKIN_Where are you?')?></a>
  </div>
  <div class="touch_tab touch_tab_middle display_none">
  <input type="text" name="location_info" class=" checkinEditLocation"/>
  </div>
  <div class="touch_tab touch_tab_middle display_none">
    <span class="checkinLoader"><?php echo $this->translate('CHECKIN_Getting location...'); ?></span>
  </div>
  <div class="clr"></div>
</div>
<div class="display_none">

  <div class="checkin_choice_cont_tpl" style="display: none;">
    <div class="checkin-autosuggest-list">
      <ul class="checkin-autosuggest"></ul>
    </div>
    <div class="clr"></div>
    <div class="checkin-autosuggest-map display_none" style="height: 300px;"></div>
    <div style="height: 0px; display: block;"></div>
  </div>

  <ul>
    <li class="checkin_choice_tpl">
      <div class="autocompleter-choice">
        <img src="" class="checkin_choice_icon"/>
        <div class="checkin_choice_label"></div>
        <div class="clr"></div>
      </div>
    </li>
  </ul>

  <div class="checkin_checkmap_tpl checkin_custom_place">
    <?php echo $this->translate('CHECKIN_There are no places found by your keywords'); ?>
    <a class="checkin_show_map" href="javascript://"><?php echo $this->translate('CHECKIN_Mark on the map'); ?></a>
  </div>
</div>

<?php endif; ?>