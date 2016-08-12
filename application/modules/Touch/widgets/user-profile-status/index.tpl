<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>

<div id='touch_profile_status'>
	<span class="touch_profile_title">
    <?php echo $this->subject()->getTitle() ?>
	</span>
  <?php if( $this->auth ): ?>
    <span class="profile_status_text" id="user_profile_status_container">
      <?php echo $this->subject()->status ?>
    </span>
  <?php endif; ?>
</div>


<?php if( !$this->auth ): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('This profile is private - only friends of this member may view it.');?>
    </span>
  </div>
  <br />
<?php endif; ?>