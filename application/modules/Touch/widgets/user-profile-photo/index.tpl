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

<div id='touch_profile_photo'>
  <a href="<?php echo $this->subject()->getPhotoUrl(null); ?>" style="display: block;">
    <?php echo $this->itemPhoto($this->subject(), 'thumb.profile') ?>
  </a>
</div>