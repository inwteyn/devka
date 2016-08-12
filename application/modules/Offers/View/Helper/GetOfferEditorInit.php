<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: GetEmailContent.php 7244 2012-10-01 12:44:00 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_View_Helper_GetOfferEditorInit extends Zend_View_Helper_Abstract
{
  public function getOfferEditorInit()
  {
    $modulesTbl = Engine_Api::_()->getDbTable('modules', 'core');
    $coreItem = $modulesTbl->getModule('core')->toArray();

    //Activity
    if (version_compare($coreItem['version'], '4.7.0') < 0) {
      $tinyMCE = <<<CONTENT
    tinyMCE.init({
      elements: 'offer_description',
      mode: "exact",
      plugins: "emotions,table,fullscreen,media,preview,paste",
      theme: "advanced",
      theme_advanced_buttons1: "undo,redo,cleanup,removeformat,pasteword,|,code,media,image,fullscreen,preview, |, bold, italic, underline, strikethrough  ,|,link, unlink, anchor, charmap, cleanup, hr, removeformat, blockquote, separator, outdent, indent, |, table",
      theme_advanced_buttons2: "formatselect, fontselect, fontsizeselect, styleselectchar, |, justifyleft, justifycenter, justifyright, justifyfull",
      theme_advanced_buttons3: "",
      theme_advanced_toolbar_align: "center",
      theme_advanced_toolbar_location: "top",
      element_format: "html",
      width: "600px",
      height: "250px",
      convert_urls: false,
      media_strict: false,
      language: "en",
      directionality: "ltr",
      editor_deselector : "mceNoEditor"
    });
    alert(01);
CONTENT;

    } else {
      $tinyMCE = <<<CONTENT
    tinymce.init({
       selector: "textarea.offer_description",
      mode: "textareas",
      plugins: "table,fullscreen,media,preview,paste,code,image,textcolor",
      theme: "modern",
      menubar: false,
      statusbar: false,
      toolbar1: "undo,redo,removeformat,pastetext,|,code,media,image,link,fullscreen,preview",
      toolbar2: "fontselect,fontsizeselect,bold,italic,underline,strikethrough,forecolor,backcolor,|,alignleft,aligncenter,alignright,alignjustify,|,bullist,numlist,|,outdent,indent,blockquote",
      toolbar3: "",
      element_format: "html",
      width: "600px",
      height: "250px",
      convert_urls: false,
      language: "en",
      directionality: "ltr",
      editor_deselector : "mceNoEditor"
    });

CONTENT;
    }

    return $tinyMCE;
  }
}