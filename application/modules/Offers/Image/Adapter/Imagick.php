<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Imagick.php 23.10.12 10:56 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_Image_Adapter_Imagick extends Engine_Image_Adapter_Imagick
{
  public function resize($width, $height, $aspect = true)
  {
    $this->_checkOpenImage();

    $imgW = $this->_resource->getImageWidth();
    $imgH = $this->_resource->getImageHeight();

    // Keep aspect
    if( $aspect ) {
      list($width, $height) = $this->_fitImage($imgW, $imgH, $width, $height);
    }

    // Resize
    try {
      $return = $this->_resource->resizeImage($width, $height, Imagick::FILTER_LANCZOS, 1);
    } catch( ImagickException $e ) {
      throw new Engine_Image_Adapter_Exception(sprintf('Unable to resize image: %s',
          $e->getMessage()), $e->getCode());
    }

    if( !$return ) {
      throw new Engine_Image_Adapter_Exception('Unable to resize image');
    }

    return $this;
  }

  protected static function _fitImage($dstW, $dstH, $maxW, $maxH)
  {
    $delta = max($maxW / $dstW, $maxH / $dstH);
    if ($delta < 1) {
      $dstH = round($dstH * $delta);
      $dstW = round($dstW * $delta);
    }

    return array($dstW, $dstH);
  }
}
