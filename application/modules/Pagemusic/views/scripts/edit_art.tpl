<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit_art.tpl 2010-10-21 17:53 idris $
 * @author     Idris
 */
?>

<?php if ($this->photo): ?>
  <li class="file file-success">
    <span class="file-size"></span>
    <a class="file-remove" id="art_action_remove" href="javascript:void(0)" title="<?php echo $this->translate('pagemusic_Click to remove this artwork.'); ?>">
			<?php echo $this->translate('Remove'); ?>
		</a>
    <span class="file-name"><?php echo $this->translate('pagemusic_Artwork'); ?></span>
    <span class="file-info">
      <img src="<?php echo $this->photo->getPhotoUrl(); ?>" />
    </span>
  </li>
<?php endif; ?>