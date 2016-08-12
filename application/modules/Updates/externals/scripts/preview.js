/***
 * MooRainbow
 *
 * @version		1.2b2
 * @license		MIT-style license
 * @author		Djamil Legato - < djamil [at] djamil.it >
 * @infos		http://moorainbow.woolly-sheep.net
 * @copyright	Author
 * 
 *
 */

var $savechanges = false;
window.onbeforeunload = function(event) {
	if(!$('submit').disabled && !$savechanges) {
		return en4.core.language.translate('UPDATES_All unsaved changes to content will be lost');
		//return 'I\'m sorry Dave, I can\'t do that.';
	} else {
		true;
	}
 };

function activate_submit(){
			$('submit').disabled = false;
			$('submit').addClass('highlight');
			$('button').disabled = true;
			$('button').setStyle('background', '#878C9C');
}

document.styleSheets[0].disabled = true;
var $color = Array();
en4.core.runonce.add(function()
{
  $('background_box').setStyle('background-color', $('bgcolor').value);
  $('font_box').setStyle('background-color', $('fncolor').value);
  $('titles_box').setStyle('background-color', $('tlcolor').value);
  $('links_box').setStyle('background-color', $('lkcolor').value);

  //set styles
  $('background').setStyle('background-color',  $('bgcolor').value);
  $$('.fontcolors').setStyle('color', $('fncolor').value);
  $$('.msgtitles').setStyle('color', $('tlcolor').value);
  $$('.msgtitles').setStyle('border-bottom-color', $('tlcolor').value);
  $$('.msgLink').setStyle('color', $('lkcolor').value);

	var $bg = new MooRainbow('background_color', {
		'id':'backgroundColor',
		'onChange': function(color) {
			$('background_box').setStyle('background-color', color.hex);
			$('background').setStyle('background-color', color.hex);
			$('bgcolor').value = color.hex;
			activate_submit();
		}
	});

	var $fn = new MooRainbow('font_color', {
		'id':'fontColor',
		'onChange': function(color) {
			$('font_box').setStyle('background-color', color.hex);
			$$('.fontcolors').setStyle('color', color.hex);
			$('fncolor').value = color.hex;
			activate_submit();
		}
	});

	var $tl = new MooRainbow('titles_color', {
		'id':'titlesColor',
		'onChange': function(color) {
			$('titles_box').setStyle('background-color', color.hex);
			$$('.msgtitles').setStyle('color', color.hex);
			$$('.msgtitles').setStyle('border-bottom-color', color.hex);
			$('tlcolor').value = color.hex;
			activate_submit();
		}
	});

	var $lk = new MooRainbow('links_color', {
		'id':'linksColor',
		'onChange': function(color) {
			$('links_box').setStyle('background-color', color.hex);
			$$('.msgLink').setStyle('color', color.hex);
			$('lkcolor').value = color.hex;
			activate_submit();
		}
	});

	$$('.content-conteiner').addEvents({
		'mouseenter':function(){
			$(this).getElement('.content-edit').setStyle('display', 'inline');
		},
		'mouseleave':function(){
		$(this).getElement('.content-edit').setStyle('display', 'none');
		}
	});
});

var content  = {
		id:0,
		name:'',
		blacklist: {},

		editContent:function(content_name, content_id)
    {
			content.id = content_id;
			content.name = content_name;

			var $items =  $(content_name + '_' + content_id).getElements('.item').getProperty('class');

			var displayed = new Array();
			for(var i in $items){
				if ($items[i].substring != undefined){
					var pos = parseInt($items[i].indexOf(' '));
					displayed[i] = $items[i].substring(5, pos);
				}
			}

      he_contacts.width = 600;
      he_contacts.height = 500;

      he_contacts.myCSS = new Asset.css(en4.core.baseUrl + 'application/css.php?request=application/themes/default/theme.css');

      he_contacts.onLoad = function() {
        $('tmp_items').adopt($$('.blacklist_item'));

        $('content_tab_active').addEvent('click', function()
         {
          $('tmp_items').adopt($$('.blacklist_item'));
          $('he_contacts_list').adopt($$('.content_item'));

          $(this).setStyle('display', 'none');
          $('blacklist_tab_disabled').setStyle('display', 'none');
          $('blacklist_tab_active').setStyle('display', '');
          $('content_tab_disabled').setStyle('display', '');

          $('remove_from_blacklist').setStyle('display', 'none');
          $('add_to_blacklist').setStyle('display', '');

          $('select_all_contacs').checked = false;
          he_contacts.choose_all_contacts($('select_all_contacs'));
        });

        $('blacklist_tab_active').addEvent('click', function()
         {
           $('tmp_items').adopt($$('.content_item'));
          $('he_contacts_list').adopt($$('.blacklist_item'));

          $(this).setStyle('display', 'none');

          $('content_tab_disabled').setStyle('display', 'none');
          $('blacklist_tab_disabled').setStyle('display', '');
          $('content_tab_active').setStyle('display', '');

          $('add_to_blacklist').setStyle('display', 'none');
          $('remove_from_blacklist').setStyle('display', '');

          $('select_all_contacs').checked = false;
          he_contacts.choose_all_contacts($('select_all_contacs'));
        });

        Smoothbox.instance.positionWindow();

        // correct styles
        $('global_content_simple').setStyle('display', 'block');
        $$('.admin_home_middle')[0].getChildren('div').setStyle('margin', 'auto');
        $$('.admin_home_middle')[0].getChildren('div').setStyle('text-align', 'center');
      };

      he_contacts.onClose = function() {
        if (he_contacts.myCSS) {
          he_contacts.myCSS.destroy();
        }
      };
			he_contacts.box('updates',
                      'getContentItems',
                      'content.addToBlacklist',
                      en4.core.language.translate('UPDATES_Edit widget content list'),
                      {
												'scriptpath':'application/modules/Updates/views/scripts/',
												'blacklist':(content.blacklist[content_name] != undefined && content.blacklist[content_name].length > 0)?content.blacklist[content_name]:false,
												'content_id':content_id,
												'content_name': content_name,
												'displayed': (displayed.length > 0)?displayed:false,
												'modified':!$('submit').disabled
											},
                    	0);
		},

		addToBlacklist:function(items)
    {
			if (content.blacklist[content.name] == undefined){
				content.blacklist[content.name] = Array();
			}

			var length = content.blacklist[content.name].length;
			var count = 0;
			for (var i in items)
      {
				if (!isNaN(parseInt(items[i])))
        {
          if (content.blacklist[content.name] == '') {
            content.blacklist[content.name] += items[i];
          } else {
					  content.blacklist[content.name] += ","+items[i];
          }

					$elements = $$('.'+content.name).getElement('.item_'+items[i]);

					for(var j in $elements)
          {
						if ($elements[j] != null && $elements[j].setStyle != undefined) {
							$elements[j].setStyle('display', 'none');
						}
					}
					count++;
        }
        else {
				  // nothing...
        }
			}

			content.id = 0;
			content.name = '';

      if (count > 0) {
				$('blacklist').value = json_enc(content.blacklist);
        $('remove').value = false;
				activate_submit();
			}
		},

		removeFromBlacklist:function(items) {
			var count = 0;
      var $contentBlacklist = content.blacklist[content.name].split(',');
      var removeBlacklist = '0';
      for(var i in items){
				var key = in_array_key(items[i], $contentBlacklist);
				if (key != undefined){
					$elements = $$('.'+content.name).getElement('.item_'+items[i]);
          removeBlacklist += ',' + items[i];
					for(var j in $elements) {
						if ($elements[j] != null && $elements[j].setStyle != undefined){
							$elements[j].setStyle('display', '');
						}
					}

					count++;

					content.blacklist[content.name][key] = 0;
				}
			}

      for (var key in content.blacklist) {
        if (key != content.name) {
          delete content.blacklist[key];
        }
      }

      content.blacklist[content.name] = removeBlacklist;
			if (count > 0) {
				$('blacklist').value = json_enc(content.blacklist);
        $('remove').value = true;
				activate_submit();
			}
		}
}

function in_array_key(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return i;
    }
    return undefined;
}

function json_enc(list)
{
	var str_str = '{';
	var i=0;
	for (var widget_type in list) {
   if (i != 0) str_str +=',';
    str_str += '"' + widget_type + '":['
    + list[widget_type].toString() + "]";
    i++;
	}

	str_str += '}';

	return str_str;
}