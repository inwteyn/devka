<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: resend.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>

<h2>
  <?php echo $this->translate("Verification Email") ?>
</h2>

<?php if( $this->error ): ?>
  <p>
    <?php echo $this->translate($this->error) ?>
  </p>

  <br />

  <h3>
    <?php echo $this->htmlLink(array('route' => 'default'), $this->translate('Back')) ?>
  </h3>
<?php else: ?>
  <p>
    <?php echo $this->translate('A verification message has been sent resent to ' .
      'your email address with instructions for activating your account. Once ' .
      'you have activated your account, you will be able to sign in.'); ?>
  </p>

  <br />

  <h3>
    <?php echo $this->htmlLink(array('route' => 'default'), $this->translate('OK, thanks!')) ?>
  </h3>
<?php endif; ?>