<?php
$this->headScript()
  ->appendFile("https://maps.google.com/maps/api/js?sensor=false")
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Offers/externals/scripts/core.js');
?>
<?php if($this->cords && !empty($this->cords['lat']) && !empty($this->cords['lng'])): ?>
  <script type="text/javascript">
    offers_manager.map.lat = <?php echo $this->cords['lat']; ?>;
    offers_manager.map.lng = <?php echo $this->cords['lng']; ?>;

    en4.core.runonce.add(function() {
      offers_manager.showGMap();
    });
  </script>

  <div id="map_canvas" style="width:100%; height:250px"></div>
  <?php echo $this->htmlLink(array('route' => 'offer_map', 'offer_id' => $this->subject->getIdentity()), $this->translate('OFFERS_offer_enlarge'), array(
      'class' => 'smoothbox offer_large_map'
    ));
  ?>
<?php endif; ?>
<div class="clr"></div>
<?php if(isset($this->contacts)): ?>
  <div id="offer_address">
    <?php echo (implode(', ', $this->contacts)) ?>
  </div>
<?php endif; ?>

<?php if(isset($this->phone) && !empty($this->phone)): ?>
  <div id="offer_contact">
    <?php echo ($this->phone); ?>
  </div>
<?php endif; ?>

<?php if(isset($this->website) && !empty($this->website)): ?>
  <div id="offer_website">
    <a href="http://<?php echo ($this->website); ?>" target="_blank"><?php echo ($this->website); ?></a>
  </div>
<?php endif; ?>