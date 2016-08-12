<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 18.06.12 10:52 michael $
 * @author     Bolot
 */
?>
<script>
  function pinfeedLanding (options){
    var column_count = 3;
    if($('activity-feed')) {
      column_count = Math.floor($('activity-feed').getComputedSize().width/270);
    }
      for (var i = 0; i < column_count; block_arr[i ++] = $('pinfeed' + (i)));
      if(window.clear_pinfeed_counts == 1){
        start =1;
      }
      for(var k = start; k <options.item.length; k ++){
        var min = array[0], min_col = 0;
        for (var j = 1; j < column_count; j ++) {
          if (min > array[j]) {
            min = array[j];
            min_col = j;
          }
        }
        options.item[k].inject(block_arr[min_col], 'bottom');
        array[min_col] += parseInt(options.item[k].getComputedSize().height) + 30;
      }
      start = options.item.length;
    }
  }
</script>