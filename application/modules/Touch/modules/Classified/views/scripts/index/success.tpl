<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: success.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>

<div class='global_form'>
  <form method="post" class="global_form touchform">
    <div>
      <div>
      <h3><?php echo $this->translate('Listing Posted');?></h3>
      <p>
        <?php echo $this->translate('Your listing was successfully published. Would you like to add some photos to it?');?>
      </p>
      <br />
      <p>
        <button type='submit' onclick="Touch.goto('<?php echo $this->url(array('controller' => 'photo', 'action' => 'upload', 'classified_id' => $this->classified->getIdentity()), 'classified_extended', true)?>');return false;"><?php echo $this->translate('Add Photos');?></button>
        <?php echo $this->translate('or');?>
        <a href='<?php echo $this->url(array('action' => 'manage'), 'classified_general', true) ?>' class="touchajax">
          <?php echo $this->translate('continue to my listing');?>
        </a>
      </p>
    </div>
    </div>
  </form>
</div>