<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-10-21 17:53 idris $
 * @author     Idris
 */
?>
<?php

$modulesTbl = Engine_Api::_()->getDbTable('modules', 'core');
$coreItem = $modulesTbl->getModule('core')->toArray();
if(version_compare($coreItem['version'], '4.8.10')>=0){
    $this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flowplayer-3.2.13.min.js');

}else{
    $this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flashembed-1.0.1.pack.js');
}

$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/swfobject/swfobject.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Store/externals/standalone/audio.js');

$this->headTranslate(
    array(
        'Add New Songs', 'Choose music from your computer to add to this playlist.', 'Edit Playlist',
        'Edit your playlist title, description, change playlist artwork, remove and order songs.'
    )
);
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Store/externals/styles/music.css');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Store/externals/styles/main.css');
?>



<?php
echo $this->partial(
    '_Player.tpl',
    array('songs' => $this->audios)
)
?>





