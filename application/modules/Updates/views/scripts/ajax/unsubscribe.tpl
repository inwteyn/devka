<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: module.tpl 2010-07-02 18:53 mirlan $
 * @author     Mirlan
 */
?>

<div class="layout_middle">
<?php if ($this->success == 1)
{
	echo $this->translate('UPDATES_UNSUBSCRIPTION_SUCCESS_MESSAGE');
}
else
{
	echo $this->translate('UPDATES_UNSUBSCRIPTION_ERROR_MESSAGE');
}
?>
</div>