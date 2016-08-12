
<div class="headline">
    <h2>
        <?php echo $this->translate('My location');?>
    </h2>
    <div class="tabs">
        <?php
        // Render the menu
        echo $this->navigation()
            ->menu()
            ->setContainer($this->navigation)
            ->render();
        ?>
    </div>
</div>

<div class="global_form">
    <?php if ($this->saveSuccessful): ?>
        <h3><?php echo $this->translate('Settings were successfully saved.');?></h3>
    <?php endif; ?>
    <?php echo $this->form->render($this) ?>
</div>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC1Z-tkFJMhU--2t-E6sJNA9nUxLO1cTYA&libraries=places"></script>


<script type="text/javascript">
    var defaultBounds = new google.maps.LatLngBounds(
        new google.maps.LatLng(-33.8902, 151.1759),
        new google.maps.LatLng(-33.8474, 151.2631));

    var input = document.getElementById('auto_com_adress');
    var options = {
        types: ['geocode']
    };
    autocomplete = new google.maps.places.Autocomplete(input, options);
   
</script>