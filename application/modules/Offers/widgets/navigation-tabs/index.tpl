<div class="headline">
  <h2>
    <?php echo $this->translate("OFFERS_Offers"); ?>
  </h2>
  <div class="tabs">
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
    <div class="offer_navigation_loader hidden" id="offer_navigation_loader">
      <?php echo $this->htmlImage($this->baseUrl().'/application/modules/Offers/externals/images/loader.gif'); ?>
    </div>
  </div>
</div>