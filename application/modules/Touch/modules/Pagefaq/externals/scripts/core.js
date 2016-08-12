/* $Id: core.js 2011-09-28 15:18 ratbek $ */

function toggle_page_edit_faq(link_hide, link_show, tab_id, tab_desc_id, visible)
{
	if (visible)
  {
		$(tab_id).setStyle('display', 'block');
		$(tab_id).removeClass('hidden');
		$(tab_desc_id).setStyle('display', 'none');
		$(tab_desc_id).addClass('hidden');

    $$('.box_faq_class').setStyle('display', 'block');
    $$('.question_faq_class').setStyle('display', 'block');
    $$('.answer_faq_class').setStyle('display', 'block');
    $$('.edit_faq_class').setStyle('display', 'inline');
    $$('.delete_faq_class').setStyle('display', 'inline');
    $('add_new_faq_id').setStyle('display', 'block');
    $('description_form_faq').setStyle('display', 'block');
    $('description_label_faq').setStyle('display', 'block');
    $('save_description_faq').setStyle('display', 'block');
	}
  else
  {
		$(tab_id).setStyle('display', 'none');
		$(tab_id).addClass('hidden');
		$(tab_desc_id).setStyle('display', 'block');
		$(tab_desc_id).removeClass('hidden');

    $$('.box_faq_class').setStyle('display', 'none');
    $$('.question_faq_class').setStyle('display', 'none');
    $$('.answer_faq_class').setStyle('display', 'none');
    $$('.edit_faq_class').setStyle('display', 'none');
    $$('.delete_faq_class').setStyle('display', 'none');
    $('add_new_faq_id').setStyle('display', 'none');
    $('description_form_faq').setStyle('display', 'none');
    $('description_label_faq').setStyle('display', 'none');
    $('save_description_faq').setStyle('display', 'none');
	}
	$(link_hide).setStyle('display', 'none');
	$(link_hide).addClass('hidden');
	$(link_show).setStyle('display', 'inline');
	$(link_show).removeClass('hidden');

};