<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: form.tpl 2011-09-01 13:17:53 kirill $
 * @author     Kirill
 */

?>
<?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
<?php if( @$this->closeSmoothbox ): ?>
	<script type="text/javascript">
  	TB_close();
	</script>
<?php endif; ?>
