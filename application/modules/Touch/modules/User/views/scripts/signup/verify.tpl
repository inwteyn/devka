<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: verify.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>



<?php if( $this->status ): ?>

  <script type="text/javascript">
    setTimeout(function() {
      parent.window.location.href = '<?php echo $this->url(array(), 'user_login', true); ?>';
    }, 5000);
  </script>

  <?php echo $this->translate("Your account has been verified.  Please click %s to login, or wait to be redirected.",
      $this->htmlLink(array('route'=>'user_login'), $this->translate("here"))) ?>

<?php else: ?>

  <div class="error">
    <span>
      <?php echo $this->translate($this->error) ?>
    </span>
  </div>

<?php endif;