function initCompleter(href) {
    $(document.body).addEvent('click', function (e) {
        if (e.target.get('id') != 'sponsor') {
            $('hecontest-pages-popup').setStyle('display', 'none');
        }
    });

    var popup = $('hecontest-pages-popup');
    popup.inject($('sponsor-element'));

    $('sponsor_type-1').addEvent('click', function () {
        $('sponsor_href-wrapper').setStyle('display', 'block');
        $('sponsor_url').set('value', '');
    });
    $('sponsor_type-0').addEvent('click', function () {
        $('sponsor_href-wrapper').setStyle('display', 'none');
        $('sponsor_url').set('value', '');
    });

    $('sponsor').addEvent('keyup', function (e) {

        if (!$('sponsor_type-0').checked) {
            return;
        }

        var sponsor = $(this);
        var target = $(this).get('value').trim();

        if (target.length <= 0)
            return;

        if (!sponsor.hasClass('small-loader')) {
            sponsor.addClass('small-loader')
        }

        new Request.JSON({
            url: href,
            method: 'post',
            data: {
                target: target,
                format: 'json'
            },
            onSuccess: function (response) {
                if (response.status) {
                    var pages = response.pages;
                    popup.getElement('ul').set('html', '');
                    for (var i = 0; i < pages.length; i++) {
                        var li = new Element('li', {'class': 'popup-list-item'});
                        li.set('text', pages[i].name);
                        li.store('url', pages[i].url);

                        li.store('id', pages[i].id);
                        li.inject(popup.getElement('ul'));
                        li.addEvent('click', function () {
                            var self = $(this);
                            $('sponsor').set('value', self.get('html'));
                            $('sponsor_url').set('value', self.retrieve('url'));
                            $('hecontest-pages-popup').setStyle('display', 'none');
                        });
                    }
                    popup.setStyle('display', 'block');
                }
                if (sponsor.hasClass('small-loader')) {
                    sponsor.removeClass('small-loader')
                }
            },
            onFail: function () {
                if (sponsor.hasClass('small-loader')) {
                    sponsor.removeClass('small-loader')
                }
            }
        }).send();

    });
}

function process(id, el, status) {
    if(status == 'remove') {
        if(!confirm('Are you shure?')) {
            return;
        }
    }
    new Request.JSON({
        url: en4.core.baseUrl + 'admin/hecontest/index/action',
        method: 'post',
        data: {
            id: id,
            status:status,
            format: 'json'
        },
        onSuccess: function (response) {
            if(response.status) {
                var parent = $(el).getParent('div').getParent('div');
                parent.getElement('span#status').set('text', response.message);
                $(el).setStyle('display', 'none');
                if(status == 'approved') {
                    parent.getElement('a#decline').setStyle('display', 'inline');
                } else if(status == 'pending') {
                    parent.getElement('a#approve').setStyle('display', 'inline');
                } else if(status == 'remove') {
                    parent.destroy();
                }
            }
            console.log(response);
        },
        onFail: function () {

        }
    }).send();
}

function showDetails(id) {
    var $popup = $('participant-details');

    showPopup();

    new Request.JSON({
        url: en4.core.baseUrl + 'admin/hecontest/index/details',
        method: 'post',
        data: {
            id: id,
            format: 'json'
        },
        onSuccess: function (response) {
            if(response.status) {
                $('participant-img').getElement('a').setStyle('background-image', 'url("'+response.img+'")');
                $('participant-descr').getElement('p').set('text', response.descr);
            }
            console.log(response);
        },
        onFail: function () {

        }
    }).send();
}
function showPopup() {
    var $screen = $('participants-screen');
    var $popup = $('participant-details');

    $screen.setStyle('display', 'block');
    $popup.setStyle('display', 'block');

    /*if(!isIE()) {
        $popup.setStyle('transform', 'translate(0px, -150%)');
        $popup.setStyle('-webkit-transform', 'translate(0px,-150%)');
        $popup.setStyle('-ms-transform', 'translate(0px,-150%)');
    }*/

    setTimeout(function () {
        $popup.setStyle('transform', 'translate(0,0)');
        $popup.setStyle('-webkittransform', 'translate(0,0)');
        $popup.setStyle('-ms-transform', 'translate(0,0)');
        $popup.setStyle('display', 'block');
        if(isIE()) {
            $popup.setStyle('width', 'auto');
            $popup.setStyle('height', 'auto');
            $popup.setStyle('left', '35%');
        }
    }, 100);
}
function hidePopup() {
    var $screen = $('participants-screen');
    var $popup = $('participant-details');


   /* $popup.setStyle('transform', 'translate(0, -150%)');
    $popup.setStyle('-webkit-transform', 'translate(0,-150%)');
    $popup.setStyle('-ms-transform', 'translate(0,-150%)');*/

    setTimeout(function () {
        $screen.setStyle('display', 'none');
        $popup.setStyle('display', 'none');
    }, 100);
}
function isIE() {
    if (navigator.userAgent.indexOf('MSIE 9.0') != -1 || navigator.userAgent.indexOf('MSIE 8.0') != -1) {
        return true;
    }
    return false;
}