arr = [0, 0, 0];
function pinfeed_store(options) {
    var $main_div = $('stores-icons');
    if ($main_div) {
        column_count = Math.floor($main_div.getComputedSize().width / 275);
    }
    else {
        column_count = 3;
    }

    if (column_count < 3) {
        var w = ((100 / column_count)) + '%';
        for (var i = 1; i <= column_count; i++) {
            var col = $('store-pinfeed' + i);
            if (col) {
                col.setStyle('width', w);
            }
        }
    }

    if (options.bottom == 1) {
        options.item[0].inject($('store-pinfeed1'), 'top');
    } else {
        var block_arr = [];
        start = 0;
        if(options.page){
            column_count = 3;
        }
        for (i = 0; i < column_count; block_arr[i++] = $('store-pinfeed' + (i)));
        if (window.clear_pinfeed_counts == 1) {
            start = 1;
        }
        for (var k = start; k < options.item.length; k++) {
            var min = arr[0], min_col = 0;
            for (var j = 1; j < column_count; j++) {
                if (min > arr[j]) {
                    min = arr[j];
                    min_col = j;
                }
            }

            options.item[k].inject(block_arr[min_col], 'bottom');

            arr[min_col] += parseInt(options.item[k].getComputedSize().height) + 30;

        }
        start = options.item.length;
      window.domready_for_pin_store = 1;
    }
}