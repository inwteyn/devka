<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Gd.php 23.10.12 10:56 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_Image_Adapter_Gd extends Engine_Image_Adapter_Gd
{
  public function resize($width, $height, $aspect = true)
  {
    $this->_checkOpenImage();

    $imgW = $this->_width;
    $imgH = $this->_height;

    // Keep aspect
    if ($aspect) {
      list($width, $height) = $this->_fitImage($imgW, $imgH, $width, $height);
    }

    // Create new temporary image
    self::_isSafeToOpen($width, $height);
    $dst = imagecreatetruecolor($width, $height);

    // Try to preserve transparency
    self::_allocateTransparency($this->_resource, $dst, $this->_format);

    // Resize
    if (!imagecopyresampled($dst, $this->_resource, 0, 0, 0, 0, $width, $height, $imgW, $imgH)) {
      imagedestroy($dst);
      throw new Engine_Image_Adapter_Exception('Unable to resize image');
    }

    // Now destroy old image and overwrite with new
    imagedestroy($this->_resource);
    $this->_resource = $dst;
    $this->_width = $width;
    $this->_height = $height;

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
