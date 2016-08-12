<?php $this->headScript()
  ->appendFile("https://maps.google.com/maps/api/js?sensor=false")
  ->appendFile($this->layout()->staticBaseUrl.'application/modules/Offers/externals/scripts/core.js');
?>

<h4 class="offer_large_map_head"><?php echo $this->subject->getTitle(); ?></h4>
<script type="text/javascript">
  offers_manager.map.lat = <?php echo $this->contacts['lat']; ?>;
  offers_manager.map.lng = <?php echo $this->contacts['lng']; ?>;

  en4.core.runonce.add(function(){
    offers_manager.showGMap();
  });
</script>
<div id="map_canvas" class="large_map" style="width:640px; height:460px"></div>