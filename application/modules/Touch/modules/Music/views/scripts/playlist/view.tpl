<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: view.tpl 8194 2011-01-11 04:17:52Z john $
 * @author     Steve
 */

// this is done to make these links more uniform with other viewscripts
$playlist = $this->playlist;
$songs    = $playlist->getSongs();
?>

<?php if ($this->popout): ?>
<?php $this->headTitle($playlist->getTitle(), Zend_View_Helper_Placeholder_Container_Abstract::SET) ?>
  <div class="music_playlist_popout_wrapper">
    <div class="music_playlist_info_title">
      <h3><?php echo $playlist->getTitle() ?></h3>
    </div>
    <?php echo $this->partial('_Player.tpl', array('playlist'=>$this->playlist,'popout'=>true)) ?>
  </div>
<?php return; endif; ?>
<div class="music_playlist layout_content" id="music_playlist_item_<?php echo $playlist->getIdentity() ?>">
  <div class="music_playlist_options" style="display: none">
    <?php if ($playlist->isEditable())
      echo $this->htmlLink($playlist->getHref(array('route' => 'music_playlist_specific', 'action' => 'edit')),
        $this->translate('Edit Playlist'),
        array('class'=>'buttonlink icon_music_edit'
      )) ?>
    <?php if ($playlist->isDeletable())
      echo $this->htmlLink($playlist->getHref(array('route' => 'music_playlist_specific', 'action' => 'delete')),
        $this->translate('Delete Playlist'),
        array('class'=>'buttonlink smoothbox icon_music_delete'
      )) ?>
    <?php if ($playlist->getOwner()->isSelf( Engine_Api::_()->user()->getViewer() ))
      echo $this->htmlLink($playlist->getHref(array('route' => 'music_playlist_specific', 'action' => 'set-profile')),
        $this->translate($playlist->profile ? 'Disable Profile Playlist' : 'Play on my Profile'),
        array(
          'class' => 'buttonlink music_set_profile_playlist ' . ( $playlist->profile ? 'icon_music_disableonprofile' : 'icon_music_playonprofile' )
        )
      ) ?>
  </div>

  <div class="music_playlist_info">
    <?php echo $this->partial('_Player.tpl', array('playlist'=>$playlist)) ?>
    
    <?php echo $this->touchAction("list", "comment", "core", array("type"=>"music_playlist", "id"=>$playlist->playlist_id)) ?>
  </div>
</div>

