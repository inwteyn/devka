<?php echo $this->render('_browseOffers.tpl'); ?>

<script type="text/javascript">
  en4.core.runonce.add(function () {
    var filter = '<?php echo $this->filter; ?>';
    offers_manager.filter = filter;
    var my_offers_filter = '<?php echo $this->my_offers_filter; ?>';
    offers_manager.my_offers_filter = my_offers_filter;

    if (filter == 'mine' && my_offers_filter == 'upcoming') {
      $$('.upcoming_button').addClass('active');
      $$('.past_button').removeClass('active');
    }
    else if (filter == 'mine' && my_offers_filter == 'past') {
      $$('.upcoming_button').removeClass('active');
      $$('.past_button').addClass('active');
    }
  });
</script>