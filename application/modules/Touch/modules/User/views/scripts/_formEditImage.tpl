<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _formEditImage.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>
<?php if( $this->subject()->photo_id !== null ): ?>
  <div>
    <?php echo $this->itemPhoto($this->subject(), 'thumb.profile', "", array('id' => 'lassoImg')) ?>
  </div>
  <br />
  <div id="preview-thumbnail" class="preview-thumbnail">
    <?php echo $this->itemPhoto($this->subject(), 'thumb.icon', "", array('id' => 'previewimage')) ?>
  </div>
  <script type="text/javascript">
    var uploadSignupPhoto = function() {
      $('EditPhoto').submit();
      $('EditPhoto').getElement('div div').addClass('profile-photo-upload');
      $('Filedata-wrapper').setStyle('display', 'none');
      $('buttons-wrapper').setStyle('display', 'none');
    }
  </script>

<?php endif; ?>