<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit_songs.tpl 2010-10-21 17:53 idris $
 * @author     Idris
 */
?>

<?php if (!empty($this->songs)): ?>
  <?php foreach ($this->songs as $song): ?>
    <li id="pagemusicsong_<?php echo $song->getIdentity(); ?>" class="file file-success">
      <a href="javascript:void(0)" class="song_action_remove file-remove"><?php echo $this->translate('Remove') ?></a>
      <span class="file-name"><?php echo $song->getTitle() ?></span>
      (<a href="javascript:void(0)" class="song_action_rename file-rename"><?php echo $this->translate('pagemusic_rename') ?></a>)
    </li>
  <?php endforeach; ?>
<?php endif; ?>