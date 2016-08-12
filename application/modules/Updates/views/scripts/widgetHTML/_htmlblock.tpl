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

<?php 
	$html = str_replace('[link]',	
									 '<a href="http://'
													. $_SERVER['HTTP_HOST'] . $this->baseUrl() . '">' 
													. $_SERVER['HTTP_HOST'] . $this->baseUrl() . '</a>', 
									 $this->translate('UPDATES_ADMIN_MSG_BODY'));
									 
	$html = str_replace('href=', 'style="text-decoration:none; color:'.$this->color.'" class="msgLink" href=', $html);

	echo $html;
?>
																														