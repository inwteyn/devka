<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagefaq
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit.tpl 2011-09-21 17:53 ratbek $
 * @author     Ratbek
 */
?>


<script type="text/javascript">

function editCancelFaq($ul)
{
  $ul.getElements('.edit_faq_class, .cancel_faq_class').removeEvent('click').addEvent('click', function ()
  {
    var $form = $(this).getParent('li').getElement('.form');
    var $box_faq = $(this).getParent('li').getElement('.box_faq_class');

    if ($form.getStyle('display') == 'none')
    {
      $box_faq.setStyle('display', 'none');
      $form.setStyle('display', 'block');
      $form.getElement('.cancel_faq_class').setProperty('onclick', '');
    }
    else
    {
      $form.setStyle('display', 'none');
      $box_faq.setStyle('display', 'block');
      var questionValue = $box_faq.getElement('.question_faq_class').innerHTML;
      var answerValue = $box_faq.getElement('.answer_faq_class').innerHTML;
      $form.getElement('.question_textarea_class').setProperty('value',questionValue);
      $form.getElement('.answer_textarea_class').setProperty('value',answerValue);
    }
  });
}

function saveFaq($ul)
{
  $ul.getElements('.form-faq').addEvent('submit', function (e)
  {
    e.stop();

    var $form = $(this);

    var values = {
      'format': 'json',
      'faq_id' : $form.getElement('.faq_id_class').get('value'),
      'page_id' : <?php  echo $this->page_id; ?>,
      'question' : $form.getElement('.question_textarea_class').get('value'),
      'answer' : $form.getElement('.answer_textarea_class').get('value')
    };

    var request = new Request.JSON (
    {
      secure: false,
      url: '<?php  echo $this->url(array("module" => "pagefaq", "controller" => "index", "action" => "save"), "default", true)?>',
      method: 'post',
      data: values,
      onSuccess: function(response)
      {
        var $result = response.html;
        $('box_' + $result.faq_id).getElement('.form').setStyle('display', 'none');
        $('box_' + $result.faq_id).getElement('.box_faq_class').setStyle('display', 'block');
        $('box_' + $result.faq_id).getElement('.question_faq_class').set('html', $result.question);
        $('box_' + $result.faq_id).getElement('.answer_faq_class').set('html', $result.answer);
      }
    }).send();
  });
}

function deleteFaq($ul)
{
  $ul.getElements('.delete_faq_class').addEvent('click', function ()
  {
    var confirmDelete = confirm("<?php echo $this->translate('PAGEFAQ_Are you sure you want to delete?');?>");

    if (!confirmDelete)	{
      return;
    }

    var $box_faq = $(this).getParent('li').getElement('.box_faq_class');
    var $form = $(this).getParent('li').getElement('.form');

    var request = new Request.JSON (
    {
      secure: false,
      url: '<?php  echo $this->url(array("module" => "pagefaq", "controller" => "index", "action" => "delete"), "default", true)?>',
      method: 'post',
      data: {
        'format': 'json',
        'faq_id' : $form.getElement('.faq_id_class').get('value')
      },
      onSuccess: function(response)
      {
        $('box_' + response.faq_id).destroy();

        var $boxes = $$('.box');
        if ($boxes == '') {
          $('add_new_faq_id').setStyle('margin-top', '-66px');
        }
      }
    }).send();
  });
}

en4.core.runonce.add(function ()
{
  var $boxes = $$('.box');
  if ($boxes == '') {
    $('add_new_faq_id').setStyle('margin-top', '-66px');
  }

  // ======================== for existing form =====================================
  var $ul = $$('.pagefaq_items')[0];

  editCancelFaq($ul);
  saveFaq($ul);
  deleteFaq($ul);

  // ====================== for new form ============================================
  var $newForm = $('new_form_faq_id');

  $('add_new_faq_id').addEvent('click', function ()
  {
    $newForm.setStyle('display', 'block');
    $('add_new_faq_id').setStyle('display', 'none');
    $newForm.getElement('.cancel_faq_class').setProperty('onclick', '');

  });


  $newForm.getElement('.cancel_faq_class').addEvent('click', function()
  {
    $newForm.setStyle('display', 'none');
    $('add_new_faq_id').setStyle('display', 'block');
    $newForm.getElement('.question_textarea_class').setProperty('value','');
    $newForm.getElement('.answer_textarea_class').setProperty('value','');
  });


  $newForm.getElements('.form-faq').addEvent('submit', function (e)
  {
    e.stop();

    var $form = $(this);
    var values = {
      'format': 'json',
      'faq_id' : 0,
      'page_id' : <?php  echo $this->page_id; ?>,
      'question' : $form.getElement('.question_textarea_class').get('value'),
      'answer' : $form.getElement('.answer_textarea_class').get('value')
    };

    var request = new Request.JSON (
    {
      secure: false,
      url: '<?php  echo $this->url(array("module" => "pagefaq", "controller" => "index", "action" => "save"), "default", true)?>',
      method: 'post',
      data: values,
      onSuccess: function(response)
      {
        response = response.html;
        var $faqTpl = $('new_faq_tpl');
        var $newBox = $faqTpl.clone();

        $newBox.getElement('.question_faq_class').set('html', response.question);
        $newBox.getElement('.answer_faq_class').set('html', response.answer);
        $newBox.setProperty('class', 'box');
        $newBox.getElement('.faq_id_class').setProperty('value', response.faq_id);
        $newBox.getElement('.question_textarea_class').setProperty('value', response.question);
        $newBox.getElement('.answer_textarea_class').setProperty('value', response.answer);

        $newForm.setStyle('display','none');
        $newForm.getElement('.question_textarea_class').setProperty('value','');
        $newForm.getElement('.answer_textarea_class').setProperty('value','');

        var $boxes = $$('.box');

        if ($boxes == '') {
          $newBox.set('id', 'box_'+response.faq_id).inject('pagefaq_items');
          $('add_new_faq_id').setStyle('margin-top', '-66px');
        }
        else {
          $newBox.set('id', 'box_'+response.faq_id).inject($boxes[$boxes.length-1],'after');
          $('add_new_faq_id').setStyle('margin-top', '30px');
        }

        $('add_new_faq_id').setStyle('display', 'block');
        $('add_new_faq_id').setStyle('margin-top', '30px');

        editCancelFaq($newBox);
        saveFaq($newBox);
        deleteFaq($newBox);
      }
    }).send();
  });


  $('save_description_faq').addEvent('click', function ()
  {
    var description_id = $('description_id').getProperty('value');
    
    var request = new Request.JSON (
    {
      secure: false,
      url: '<?php  echo $this->url(array("module" => "pagefaq", "controller" => "index", "action" => "savedescription"), "default", true)?>',
      method: 'post',
      data: {
        'format' : 'json',
        'description_id' : description_id,
        'page_id' : <?php  echo $this->page_id; ?>,
        'description' : window.tinyMCE.editors.descriptionFAQ.getContent()
      },
      onSuccess: function(response)
      {
        $('ok_icon').setStyle('opacity','1');
        setTimeout(function() {
          $('ok_icon').set('tween', {duration : 1000});
          $('ok_icon').tween('opacity', 0);
        }, 3000);
      }
    }).send();
  });
});

</script>

  
<div class="global_form">
  <div>
    <div>
      <h3><?php echo $this->translate("PAGEFAQ_FAQ"); ?></h3>
      <p class="form_description"><?php echo $this->translate('PAGEFAQ_Edit your Page FAQ'); ?></p>

      <div id="description_faq_id">
        <p id="description_label_faq" class="description_label_faq"><?php echo $this->translate('PAGEFAQ_Description'); ?></p>
        <img id="ok_icon" class="ok_icon" src="application/modules/Pagefaq/externals/images/ok.png" alt="Successfully saved" title="<?php echo $this->translate('PAGEFAQ_Successfully saved'); ?>">
        <?php echo $this->descriptionFAQForm->render($this); ?>
        <button id="save_description_faq" class="save_description_faq"><?php echo $this->translate('PAGEFAQ_Save'); ?></button>
      </div>
      <ul class="pagefaq_items" id="pagefaq_items">
        <?php foreach($this->allFAQs as $faq): ?>
          <li id="box_<?php echo $faq->faq_id; ?>" class="box">
            <table cellpadding="0" cellspacing="0">
              <tr>
                <td valign="top">
                  <div class="box_faq_class">
                    <a href="javascript:void(0)" class="edit_faq_class" title="<?php echo $this->translate('PAGEFAQ_Edit'); ?>"><img src="application/modules/Touch/externals/images/edit.png" alt="Edit"></a>
                    <a href="javascript:void(0)" class="delete_faq_class" title="<?php echo $this->translate('PAGEFAQ_Delete'); ?>"><img src="application/modules/Touch/externals/images/delete.png" alt="Delete"></a>
                    <p class="question_faq_class"><?php echo $faq->question; ?></p>
                    <p class="answer_faq_class"><?php echo $faq->answer; ?></p>
                  </div>
                  <div class="form" style="display: none">
                    <?php echo $this->editFAQForm->populate(array('faq_id' => $faq->faq_id, 'question_faq' => $faq->question, 'answer_faq' => $faq->answer))->render($this); ?>
                  </div>
                </td>
              </tr>
            </table>
          </li>
        <?php endforeach; ?>
      </ul>

      <div id="new_form_faq_content_id" class="new_form_faq_content_class">
        <table cellpadding="0" cellspacing="0">
         <tr>
           <td>
             <div id="new_form_faq_id" class="new_form_faq_class" style="display: none">
               <?php echo $this->createFAQForm->render($this); ?>
             </div>
           </td>
           <td valign="top">
             <button type="button" class="add_new_faq_class" id="add_new_faq_id" style="display: block" ><?php echo $this->translate('PAGEFAQ_Add new'); ?></button>
           </td>
         </tr>
        </table>
      </div>
    </div>
  </div>
</div>

<div style="display: none;">
  <ul>
    <li id="new_faq_tpl">
        <table cellpadding="0" cellspacing="0">
          <tr>
            <td valign="top">
              <div class="box_faq_class">
                <a href="javascript:void(0)" class="edit_faq_class" title="<?php echo $this->translate('PAGEFAQ_Edit'); ?>"><img src="application/modules/Touch/externals/images/edit.png" alt="Edit"></a>
                <a href="javascript:void(0)" class="delete_faq_class" title="<?php echo $this->translate('PAGEFAQ_Delete'); ?>"><img src="application/modules/Touch/externals/images/delete.png" alt="Delete"></a>
                <p class="question_faq_class"></span></p>
                <p class="answer_faq_class"></span></p>
              </div>
              <div class="form" style="display: none;">
                <?php echo $this->editFAQForm->populate(array('faq_id' => 0, 'question_faq' => '', 'answer_faq' => ''))->render($this); ?>
              </div>
            </td>
          </tr>
        </table>
      </li>
  </ul>
</div>
