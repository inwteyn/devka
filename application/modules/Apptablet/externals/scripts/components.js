/**
 * Example

 UIComponent.feed = function (params, $template)
 {
 // ...
 return $template;
 };

 */


/* Tabs Component */
//UIComponent.tabs = function (params, $template) {
//
//    var object_fields = params.info;
//
//    if (object_fields.flag) {
//        $template.find('.tab-1').html(this.pageResponseData.info.fields);
//    } else {
//        var $fieldTpl = $template.find('.field').clone();
//        var $fields = $template.find('#static-fields');
//        $fields.empty();
//        for (var order in this.pageResponseData.info.fields) {
//            var field = this.pageResponseData.info.fields[order];
//            var $field = $fieldTpl.clone();
//            $field.find('h3').html(field.title);
//            var $fieldTextTpl = $field.find('table').find('tr').clone();
//            $field.find('tbody').empty();
//            for (var textOrder in field.content) {
//                var $fieldText = $fieldTextTpl.clone();
//                var fieldText = field.content[textOrder];
//                $fieldText.find('th').html(fieldText.label);
//                $fieldText.find('td').html(fieldText.value);
//                $field.find('tbody').append($fieldText);
//            }
//            $fields.append($field);
//        }
//    }
//
//
//    var menu_item_tpl = $template.find('.tablet-profile_tabs').find('.li_tab').clone();
//    menu_item_tpl.find('.tab_title').find('span').empty();
//    $template.find('.tablet-profile_tabs').empty();
//
//    for (var key in params) {
//        if (params[key] == params.info) continue;
//        var menu_item = params[key];
//        var $menu_item = menu_item_tpl.clone();
//        $menu_item.find('a').attr(menu_item.attrs);
//        $menu_item.find('a').addClass('tab ' + menu_item.attrs.class);
//        if (menu_item.count * 1 > 0)
//            $menu_item.find('.ui-li-count').html(menu_item.count);
//        else
//            delete $menu_item.find('.ui-li-count').remove();
//
//        $menu_item.find('.tab_title').html(menu_item.label);
//        if (menu_item.active) {
//            $menu_item.find('a').addClass('ui-btn-active');
//            $menu_item.find('a').attr('data-order', key);
//        }
//        if ($menu_item.find('a').hasClass('user_tab_fields') ||
//            $menu_item.find('a').hasClass('page_tab_fields')) {
//            continue
//        }
//        $template.find('.tablet-profile_tabs').append($menu_item);
//    }
//
//    return $template;
//};

//UIComponent.timelineCover = function (params, $template) {
//    var cover_photo = params.cover_photo;
//
//    if (params.user) {
//        if (params.user.photo.profile) {
//            $template.find('.subject_photo img').attr('src', params.user.photo.profile);
//        }
//        $template.find('.subject_title').html(params.user.title);
//    }
//
//    if (params.canChange) {
//        $template.find('.cover_actions_wrapper').show();
//    }
//
//    if (params.choose) {
//        $template.find('#cover_choose').find('a').attr('href', params.choose);
//    }
//    if (params.upload) {
//        $template.find('#cover_upload').find('a').attr('href', params.upload);
//    }
//    if (params.remove) {
//        $template.find('#cover_remove').find('a').attr('href', params.remove);
//    } else {
//        delete $template.find('#cover_remove').remove();
//    }
//
//    $template.find('.cover_photo_href').html(cover_photo);
//    $template.find('.cover_photo_href').find('img').attr('style',
//        'width: 100%; position: relative;top:0 !important;' + $template.find('.cover_photo_href').find('img').attr('style')
//    );
//
//    return $template;
//};



//UIComponent._registryWall = function (params, $template)
//{
//  try {
//    params.$template = $template;
//    var $feed = $template.find('.social-feed');
//    var ins = new ActivityFeed($feed, params);
//  } catch (e){}
//
//  /**
//   * Create objects in window
//   */
//  if (!window._wall_keys){
//    window._wall_keys = [];
//  }
//  var n = window._wall_keys.length;
//  window._wall_keys[n] = '_wall_'+ n;
//  window[window._wall_keys[n]] = ins;
//  window.wall = ins;
//
//  var $cl = $template.find('.composeLink');
//  $cl.data('key', window._wall_keys[n]);
//
//  var FnVClick = function(e){
//    var el = this;
//    var we = Wall.events;
//    var tab = TabletWallEvents;
//    var action = el.getAttribute('class').split('we-')[1].split(' ')[0];
//    switch(action) {
//      case 'remove': we.remove(this); break;
//      case 'mute': we.mute(this); break;
//      case 'removeTag': we.removeTag(this); break;
//      case 'hideMenu': we.hideMenu(this); break;
//      case 'showMenu': we.showMenu(this); break;
//      case 'like': tab.like(this); break;
//      case 'unlike': tab.unlike(this); break;
//    }
//  };
//  $template.undelegate('.wall-event', 'vclick', FnVClick);
//  $template.delegate('.wall-event', 'vclick', FnVClick);
//
//  $('#wall-feed-menu').delegate('.wall-event-form', 'vclick', function(e){
//    var el = this;
//    var wef = Wall.events.form;
//    var type = el.getAttribute('class').split('wef-')[1].split(' ')[0];
//    wef.showPostForm(this, type);
//  });
//  $cl.bind('vclick', function(){Wall.events.toggleForm(this)});
//
//};