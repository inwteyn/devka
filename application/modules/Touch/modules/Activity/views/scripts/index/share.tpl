<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com/
 * @version    $Id: share.tpl 7244 2010-09-01 01:49:53Z mirlan $
 * @author     Mirlan
 */
?>
<div>
<?php echo $this->form->setAttrib('class', 'layout_content global_form touchform')->render($this) ?>
</div>

<?php if (!$this->formPosted): ?>
<br />
<div class="sharebox layout_content">
	<?php if( $this->attachment->getPhotoUrl() ): ?>
		<div class="sharebox_photo">
			<?php echo $this->htmlLink($this->attachment->getHref(), $this->itemPhoto($this->attachment, 'thumb.icon'), array('target' => '_parent')) ?>
		</div>
	<?php endif; ?>
  <div>
		<div class="sharebox_title">
			<?php echo $this->htmlLink($this->attachment->getHref(), $this->attachment->getTitle(), array('target' => '_parent')) ?>
		</div>
		<div class="sharebox_description">
			<?php echo $this->attachment->getDescription() ?>
		</div>
	</div>
</div>
  <script type="text/javascript"> 
//<![CDATA[
var toggleFacebookShareCheckbox = function(el){
    $('.composer_facebook_toggle').toggleClass('composer_facebook_toggle_active');
    $$('input[name=post_to_facebook]').set('checked', $$('span.composer_facebook_toggle')[0].hasClass('composer_facebook_toggle_active'));
}
var toggleTwitterShareCheckbox = function(){
    $$('span.composer_twitter_toggle').toggleClass('composer_twitter_toggle_active');
    $$('input[name=post_to_twitter]').set('checked', $$('span.composer_twitter_toggle')[0].hasClass('composer_twitter_toggle_active'));
}
//]]>
</script>
<?php endif; ?>