<script type="text/javascript">
  function select_link() {
    var link_wrapper = document.getElementById('referral-link');
    link_wrapper.select();
  }
</script>
<textarea id="referral-link"
          class="link"
          onkeypress="return false;"
          onkeydown="return false;"
          onkeyup="return false;"
          onClick="this.select();"><?php echo $this->referral_link; ?></textarea>


<div style="margin-top: 10px; text-align: center;">
  <?php echo $this->translate('INVITER_Referral Link Share'); ?>
</div>
<div class="addthis_toolbox addthis_default_style addthis_32x32_style">
  <a class="addthis_button_facebook"></a>
  <a class="addthis_button_twitter"></a>
  <a class="addthis_button_compact"></a>
  <a class="addthis_counter addthis_bubble_style"></a>
</div>
<script type="text/javascript">
  var addthis_config = {
    "data_track_addressbar": true
  };
  var addthis_share = {
    url: "<?php echo $this->referral_link; ?>",
    title: "<?php echo $this->translate("INVITER_Join our social network!");?>",
    description: "<?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('invite.message')); ?>"
  };

  en4.core.runonce.add(function () {
    var $textarea = $('referral-link');
    $textarea.setStyle('height', 0);
    $textarea.setStyle('height', parseInt($textarea.scrollHeight) + 'px');
    $textarea.setStyle('padding-top', '5px');
    $textarea.setStyle('padding-bottom', '5px');
  });
</script>

<?php $this->headMeta()->appendName('og:title', $this->translate("INVITER_Join our social network!")); ?>
<?php $this->headMeta()->appendName('og:description', $this->translate("Share anything to users who feel the same way about it")); ?>

<script type="text/javascript"
        src="<?php echo(_ENGINE_SSL ? 'https://' : 'http://'); ?>s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4fb1c2793e9a0bfe"></script>