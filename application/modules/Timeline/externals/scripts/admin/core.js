window.he_timeline_hack_mouse_down = false;

function restyleCoverWidgets() {
    var widgets = $$('.admin_content_widget_timeline\\.new-cover.admin_content_buildable');
    widgets.each(function (el) {
        if (!el.hasClass('he-ready')) {
            var html = 'Profile Cover Widget <span class="remove"><a href="javascript:void(0)" onclick="removeWidget($(this));">x</a></span>';
            el.getElement('span').set('html', html);
            el.getElement('span.admin_layoutbox_widget_tabbed_overtext').set('text', 'Adds a container with a tab menu. Any other blocks you drop inside it will become tabs.');
            el.addClass('he-ready');
        }
    });
}
function restyleFeedWidgets() {
    var widgets = $$('.admin_content_widget_timeline\\.new-feed.admin_content_buildable');
    widgets.each(function (el) {
        if (!el.hasClass('he-ready')) {
            var html = 'Profile Feed Widget <span class="remove"><a href="javascript:void(0)" onclick="removeWidget($(this));">x</a></span>';
            el.getElement('span').set('html', html);
            el.addClass('he-ready');
        }
    });
}
window.addEvent('domready', function () {
    $$('.admin_content_widget_timeline\\.new-cover').each(function (el) {
        el.addClass('admin_content_widget_core.container-tabs');
    });
    $$('.admin_content_widget_timeline\\.new-feed').each(function (el) {
        el.addClass('admin_content_widget_core.container-tabs');
    });


    document.body.addEvent('mouseup', function (e) {
        if (!window.he_timeline_hack_mouse_down)
            return;
        if (e.target.hasClass('admin_content_widget_timeline.new-cover')) {
            setTimeout(function () {
                restyleCoverWidgets();
            }, 100);
        }
        if (e.target.hasClass('admin_content_widget_timeline.new-feed')) {
            setTimeout(function () {
                restyleFeedWidgets();
            }, 100);
        }
        window.he_timeline_hack_mouse_down = false;
    });


    $$('.admin_content_stock_draggable').each(function (el) {
        el.addEvent('mousedown', function (e) {
            window.he_timeline_hack_mouse_down = true;
        });
    });



    restyleCoverWidgets();
    restyleFeedWidgets();
});
