/* $Id: core.js 2011-07-13 16:01 ratbek $ */

function toggle_page_edit_contact(link_hide, link_show, tab_id, tab_desc_id, visible)
{
	if (visible){
		$(tab_id).setStyle('display', 'block');
		$(tab_id).removeClass('hidden');
		$(tab_desc_id).setStyle('display', 'none');
		$(tab_desc_id).addClass('hidden');
    $('TinyMCE_decription').setStyle('display', 'block');
	}else{
		$(tab_id).setStyle('display', 'none');
		$(tab_id).addClass('hidden');
		$(tab_desc_id).setStyle('display', 'block');
		$(tab_desc_id).removeClass('hidden');
    $('TinyMCE_decription').setStyle('display', 'none');
	}
	$(link_hide).setStyle('display', 'none');
	$(link_hide).addClass('hidden');
	$(link_show).setStyle('display', 'inline');
	$(link_show).removeClass('hidden');
};