<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: create.tpl 2010-07-02 18:53 mirlan $
 * @author     Mirlan
 */
?>

<?php $this->headLink()
  ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/Updates/externals/styles/preview.css');
?>

<script type="text/javascript">
function insert_template(){
	parent.tinyMCE.get('message').setContent(<?php echo Zend_Json::encode($this->template->message); ?>);
	parent.$('subject').set('value', <?php echo Zend_Json::encode($this->template->subject); ?>);
	parent.Smoothbox.close();
}

document.styleSheets[0].disabled = true;
</script>

<div class="global_form_popup" style="width: 720px; padding: 5px;">
	<div style="padding: 5px; font-weight:bold; border-bottom:1px solid #555555; margin-bottom:10px;">
		<?php echo $this->preview['subject']; ?>
		<div style="font-size: 11px; font-weight:normal;">
			<?php if (Engine_String::strlen($this->template->description) >0):?>
				<?php echo $this->translate('Description') . ': ' . $this->template->description . '<br/>'; ?>
			<?php endif;?>
			<?php echo $this->translate('Created') . ': ' . $this->locale()->toDateTime($this->template->creation_date, array('size'=>'short')); ?>
		</div>
	</div>

	<div style="padding: 10px; background:#ffffff;  -moz-border-radius: 1px 1px 1px 1px; border: 1px solid #C8D8E0;">
		<?php echo $this->preview['message']; ?>
	</div>

	<div style="clear:both"></div>

	<div style='clear:both;margin: 5px;'>
		<?php echo $this->form->render($this); ?>
	</div>

</div>
<?php	exit();?>