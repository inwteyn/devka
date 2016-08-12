<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: _browseUsers.tpl 9979 2013-03-19 22:07:33Z john $
 * @author     Bolot
 */

?>
<?php echo Engine_Api::_()->getApi('gmap', 'headvancedmembers')->getMapJS(); ?>
<?php
 $markers1 = Engine_Api::_()->getApi('gmap', 'headvancedmembers')->getMarkers($this->users);
 $bounds1 = Engine_Api::_()->getApi('gmap', 'headvancedmembers')->getMapBounds($markers1);
 $markers = (!empty($markers1)) ? Zend_Json_Encoder::encode($markers1) : '';
 $bounds  = Zend_Json_Encoder::encode($bounds1);
?>
<div style="position: relative;">
  <div id="page_map_cont" style="overflow: hidden;">
    <div id="map_canvas" class="browse_gmap" style="position:absolute;top:1000000px;">
      <?php if (!($markers > 0)): ?>
        <ul class="form-notices"><li><?php echo $this->translate('There is no location data'); ?></li></ul>
      <?php endif; ?>
    </div>
  </div>
</div>
<script type="text/javascript">
  en4.core.runonce.add(function() {
    pages_map.construct( null, <?php echo $markers; ?>, 4, <?php echo $bounds; ?> );
  });
</script>


<script type="text/javascript">
  page = '<?php echo sprintf('%d', $this->page) ?>';
  totalUsers = '<?php echo sprintf('%d', $this->totalUsers) ?>';
  userCount = '<?php echo sprintf('%d', $this->userCount) ?>';
  $$('#browsemembers_ul_advs_large li').each(function(element){
    element.addEvents({
      mouseover: function(){
        $('user_button_'+element.get('rev')).show();
      },
      mouseleave: function(){
        $('user_button_'+element.get('rev')).hide();
      }
    })

  });
</script>
