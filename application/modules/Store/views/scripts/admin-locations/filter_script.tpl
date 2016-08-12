<script type="text/javascript">
    en4.core.runonce.add(function () {
        document.allFlag = '<?php echo $this->flag; ?>';
    });

    function isNumber(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }
    function filterRequest(parent_id, value, type) {
        var url = '';
        if(type == 1) {
            url = '<?php echo $this->url(array('module' => 'store', 'controller' => 'locations', 'action' => 'index'), 'admin_default', true);?>';
        } else {
            url = '<?php echo $this->url(array('module' => 'store', 'controller' => 'locations', 'action' => 'all'), 'admin_default', true);?>';
        }
        new Request.HTML({
            url: url,
            method: 'post',
            evalScript:false,
            data: {
                parent_id: parent_id,
                location_name: value
            },
            onRequest: function () {
            },
            onSuccess: function (responseTree, responseElements, responseHtml, responseJavascript) {
                var $container = new Element('div', {'html': responseHtml});
                var content = $container.getElement('tbody.only-locations');
                var current = $('only-locations');
                var fromServer = content.get('html');
                current.set('html', fromServer);
                Smoothbox.bind(current);

                var pagination = $container.getElement('div.locations-pagination');
                if(pagination) {
                    var cP = $('locations-pagination');
                    if(cP) {
                        var fromServerP = pagination.get('html');
                        cP.set('html', fromServerP);
                    }
                }
            }
        }).send();
    }

    var filterLocations = function(e, nc) {
        var el = $('location-name');

        var parent_id = Number('<?php echo ($this->parent_id) ? $this->parent_id : 0; ?>')   ;
        if(!isNumber(parent_id)) {
            parent_id = 0;
        }

        var remove_keys = [8, 46];
        var code = e.keyCode;
        if(!remove_keys.contains(code) && (code < 48 || code > 90 )) {
            return;
        }
        var value = el.value;
        filterRequest(parent_id, value, nc);
    }

</script>