<?php

 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit_photo.tpl 2010-07-02 17:53 michael $
 * @author     Michael
 */

if ($this->photo): ?>

  <li class="file file-success">
    <span class="file-size"></span>
    <a class="file-remove" id="action_remove" href="javascript:Pageevent.removePhoto(<?php echo $this->photo->getIdentity()?>);" title="<?php echo $this->translate('Click to remove this entry.'); ?>">
			<?php echo $this->translate('Remove'); ?>
		</a>
    <span class="file-name"><?php echo $this->translate('PAGEEVENT_PHOTO'); ?></span>
    <span class="file-info">
      <img src="<?php echo $this->photo->map(); ?>" />
    </span>
  </li>

<?php endif;