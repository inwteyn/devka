<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

?>
<?php
$modulesTbl = Engine_Api::_()->getDbTable('modules', 'core');
$coreItem = $modulesTbl->getModule('core')->toArray();

//Activity
if (version_compare($coreItem['version'], '4.7.0') < 0):
  ?>
  <script type="text/javascript">
    en4.core.runonce.add(function (){
      tinyMCE.init({
        mode: "exact",
        plugins: "emotions,table,fullscreen,media,preview,paste",
        theme: "advanced",
        theme_advanced_buttons1: "undo,redo,cleanup,removeformat,pasteword,|,code,media,image,fullscreen,preview",
        theme_advanced_buttons2: "",
        theme_advanced_buttons3: "",
        theme_advanced_toolbar_align: "left",
        theme_advanced_toolbar_location: "top",
        element_format: "html",
        height: "225px",
        convert_urls: false,
        media_strict: false,
        elements: "document_description",
        language: "en",
        directionality: "ltr"
      });
    });

  </script>
<?php else:?>
  <script type="text/javascript">
    en4.core.runonce.add(function (){
      tinyMCE.init({
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
        directionality: "ltr"
      });
    });

  </script>
<?php endif;?>

<?php echo $this->editForm->render($this); ?>