<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Register
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: add-users.tpl  04.12.12 12:44 TeaJay $
 * @author     Taalay
 */
?>

<div style="float: right;">
  <?php echo $this->htmlLink($this->url(array(), 'register_url', true), $this->translate('Main Page'),
    array('style' => 'font-size: 15px; color: red; font-weight: bold')
  )?>
</div>

<?php echo $this->form->render($this); ?>