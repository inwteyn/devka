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
<div id='profile_status'>
  
  <h2 class="like_status_header">
    <?php echo $this->subject()->getTitle() ?>
  </h2>
  <?php echo $this->touchLikeButton($this->subject); ?>
  <div class="clr"></div>
  
</div>