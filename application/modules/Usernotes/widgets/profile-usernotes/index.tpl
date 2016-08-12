<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Usernotes
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-07-02 17:53 vadim $
 * @author     Vadim
 */
?>

<?php
  $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Usernotes/externals/scripts/core.js');

  $this->langvars = Zend_Json::encode(array(
    'your_note_saved' => $this->translate('Your note saved!'),
    'empty_text' => $this->translate('Please type your note!'),
  ));
?>

<script type="text/javascript">

en4.core.runonce.add(function() {

  he_usernotes.construct (
  <?php if ( !empty($this->usernote) ) { ?>
    <?php echo  $this->usernote->usernote_id; ?>,
    <?php echo  $this->usernote->owner_id; ?>,
    <?php echo  $this->usernote->user_id; ?>,
    <?php echo  $this->note_js; ?>,
    'view',
    <?php echo $this->urls_js?>
  <?php } else { ?>
    0, 0, 0, '', 'edit', <?php echo $this->urls_js?>, <?php echo $this->langvars?>
  <?php } ?>
  );

  $('usernotes_delete').addEvent('click', function() {
    Smoothbox.open($('delete_note_confrim'), {mode: 'Inline', width: 350, height: 100});
  });

  $('usernotes_edit').addEvent('click', function() {
    he_usernotes.toggleEdit();
  });

  $('he_usernotes_cancel').addEvent('click', function() {
    he_usernotes.cancel();
  });

  $('he_usernotes_save').addEvent('click', function() {
    he_usernotes.save();
  });

});

</script>


<ul class="usernotes_profile_sidebar">
  <li>
    <div class="usernotes_body">

      <?php if($this->usernote) { ?>


            <div id="usernotes_info">
                <div id="he_usernotes_date" class="he_usernotes_date">
                    <?php echo $this->timestamp($this->usernote->creation_date) ?>&nbsp;
                </div>
                <div id="he_usernotes_text">
                  <?php echo nl2br($this->usernote->note); ?>
              </div>
            </div>

            <div id="he_usernotes_form" style="visibility:hidden; opacity:0;">
              <?php print $this->form; ?>
            </div>

            <div class="usernotes_menu">
                <a href="javascript://" class="usernotes_edit" id="usernotes_edit" title="<?php echo $this->translate('Edit Note'); ?>"></a>
                <a href="javascript://" class="usernotes_delete" id="usernotes_delete" title="<?php echo $this->translate('Delete Note'); ?>"></a>
            </div>

        <?php } else { ?>

            <div id="usernotes_info" class="usernotes_info">
                <div id="he_usernotes_date" class="he_usernotes_date"></div>
            </div>

            <div id="he_usernotes_text" style="visibility:hidden; opacity:0;">
            </div>

            <div id="he_usernotes_form">
              <?php print $this->form; ?>
            </div>

            <div class="usernotes_menu">
                <a href="javascript://" class="usernotes_edit" id="usernotes_edit" style="visibility:hidden; opacity:0;">&nbsp;</a>
                <a href="javascript://" class="usernotes_delete" id="usernotes_delete" style="visibility:hidden; opacity:0;">&nbsp;</a>
            </div>

      <?php } ?>

    </div>
  </li>

</ul>



<div style="display: none;">
  <div id="delete_note_confrim">
    <div class="title" style="font-weight: bold; font-size: 11pt; margin-bottom: 10px;"><?php echo $this->translate('Delete Note'); ?></div>
    <div>
      <?php echo $this->translate('Are you sure you want to delete this note?'); ?><br /><br />
    </div>
    <div align="center">
      <button type="button" onclick="he_usernotes.delete_note();"><?php echo $this->translate('Delete'); ?></button>
      <button type="button" onclick="Smoothbox.close()"><?php echo $this->translate('Cancel'); ?></button>
    </div>
  </div>
</div>

<div class="clr"></div>

