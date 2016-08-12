<?php echo Engine_Api::_()->getApi('gmap', 'page')->getMapJS(); ?>

<script type="text/javascript">
  var current_marker = {};
  current_marker.markers = <?php echo $this->markers; ?>;
  current_marker.bounds = <?php echo $this->bounds; ?>;

  en4.core.runonce.add(function () {
      var more_address_btn = new Element('a', {
          'class': 'add_more_address_btn',
          'html': en4.core.language.translate('Add more address'),
          'address_counter': 0
      });

      more_address_btn.addEvent('click', function(){
          var address_counter = this.get('address_counter').toInt() + 1;
          $('additional_country_' + address_counter + '-wrapper').show();
          $('additional_state_' + address_counter + '-wrapper').show();
          $('additional_city_' + address_counter + '-wrapper').show();
          $('additional_street_' + address_counter + '-wrapper').show();
          if(address_counter == 4){
              this.destroy();
          }
          this.set('address_counter', address_counter)
      });

      $('website-wrapper').getParent('.form-elements').insertBefore(more_address_btn, $('website-wrapper'));

      window.setTimeout(function () {
          for(i = 1; i < 5; i++){
              if(!$('additional_country_' + i).value && !$('additional_state_' + i).value && !$('additional_city_' + i).value && !$('additional_street_' + i).value){
                  $('additional_country_' + i + '-wrapper').hide();
                  $('additional_state_' + i + '-wrapper').hide();
                  $('additional_city_' + i + '-wrapper').hide();
                  $('additional_street_' + i + '-wrapper').hide();
              } else {
                  var counter = more_address_btn.get('address_counter').toInt();
                  more_address_btn.set('address_counter', counter + 1);
              }
          }
      }, 500);

  });
</script>

<a href="javascript://" class="page_edit_form_map_show" onclick='pages_map.showEditMap(null, current_marker.markers, 2, current_marker.bounds, true);'><?php echo $this->translate('PAGE_Show map'); ?></a>
<a href="javascript://" class="page_edit_form_map_hide display_none" onclick="pages_map.hideEditMap();"><?php echo $this->translate('PAGE_Hide map'); ?></a>
<br/>
<div id="map_canvas" class="page_map display_none" style="width: 500px; height: 300px"></div>