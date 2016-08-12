<?php

/**
 * Created by PhpStorm.
 * User: USER
 * Date: 17.10.13
 * Time: 8:53
 */
class Avatarstyler_Api_Core extends Core_Api_Abstract
{

  public function getLayer($currentPhotoId)
  {
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
//    $currentPhotoId = $settings->getSetting('avatarstyler.current.photo.id', 0);
    if($currentPhotoId) {
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
        $currentPhoto = $filesTable->getFile($currentPhotoId);

        if ($currentPhoto) {

            return  $currentPhoto->storage_path;
        } else {
            return 'application/modules/Avatarstyler/externals/images/layer_default.png';
        }

    }
  }

}