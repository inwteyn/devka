<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit_photo.tpl 2012-11-05 17:53 ulan t $
 * @author     Ulan T
 */
?>

<?php if ($this->photo): ?>
<li class="file file-success">
    <span class="file-size"></span>
    <a class="file-remove" id="blog_photo_action_remove" href="javascript:void(0)" title="<?php echo $this->translate('Click to remove this entry.'); ?>">
      <?php echo $this->translate('Remove'); ?>
    </a>
    <span class="file-name"><?php echo $this->translate('Main Photo'); ?></span>
    <span class="file-info">
      <img src="<?php echo $this->photo->getPhotoUrl(); ?>" />
    </span>
</li>
<?php endif; ?>