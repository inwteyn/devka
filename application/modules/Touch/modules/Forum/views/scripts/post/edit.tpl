<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: edit.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     Sami
 */
?>
<script type="text/javascript">
function updateUploader()
{
  if($('photo_delete').checked) {
    $('photo_group-wrapper').style.display = 'block';
  }
  else 
  {
    $('photo_group-wrapper').style.display = 'none';
  }
}
</script>
<tbody><tr class="mceFirst" role="presentation"><td role="presentation" class="mceToolbar mceLeft mceFirst mceLast"><div tabindex="-1" id="blog_body_toolbargroup" role="group" aria-labelledby="blog_body_toolbargroup_voice"><span role="application"><span id="blog_body_toolbargroup_voice" class="mceVoiceLabel" style="display:none;">Toolbar</span><table aria-pressed="false" aria-disabled="false" id="blog_body_toolbar1" class="mceToolbar mceToolbarRow1 Enabled" role="presentation" tabindex="-1" align="" cellpadding="0" cellspacing="0"><tbody><tr><td class="mceToolbarStart mceToolbarStartButton mceFirst"><span><!-- IE --></span></td><td style="position: relative"><a aria-disabled="false" tabindex="-1" role="button" id="blog_body_undo" href="javascript:;" class="mceButton mce_undo mceButtonEnabled" onmousedown="return false;" onclick="return false;" aria-labelledby="blog_body_undo_voice" title="Undo (Ctrl+Z)"><span class="mceIcon mce_undo"></span><span class="mceVoiceLabel mceIconOnly" style="display: none;" id="blog_body_undo_voice">Undo (Ctrl+Z)</span></a></td><td style="position: relative"><a aria-disabled="true" tabindex="-1" role="button" id="blog_body_redo" href="javascript:;" class="mceButton mce_redo mceButtonDisabled" onmousedown="return false;" onclick="return false;" aria-labelledby="blog_body_redo_voice" title="Redo (Ctrl+Y)"><span class="mceIcon mce_redo"></span><span class="mceVoiceLabel mceIconOnly" style="display: none;" id="blog_body_redo_voice">Redo (Ctrl+Y)</span></a></td><td style="position: relative"><a tabindex="-1" role="button" id="blog_body_cleanup" href="javascript:;" class="mceButton mceButtonEnabled mce_cleanup" onmousedown="return false;" onclick="return false;" aria-labelledby="blog_body_cleanup_voice" title="Cleanup messy code"><span class="mceIcon mce_cleanup"></span><span class="mceVoiceLabel mceIconOnly" style="display: none;" id="blog_body_cleanup_voice">Cleanup messy code</span></a></td><td style="position: relative"><a tabindex="-1" role="button" id="blog_body_removeformat" href="javascript:;" class="mceButton mceButtonEnabled mce_removeformat" onmousedown="return false;" onclick="return false;" aria-labelledby="blog_body_removeformat_voice" title="Remove formatting"><span class="mceIcon mce_removeformat"></span><span class="mceVoiceLabel mceIconOnly" style="display: none;" id="blog_body_removeformat_voice">Remove formatting</span></a></td><td style="position: relative"><span class="mceSeparator" role="separator" aria-orientation="vertical" tabindex="-1"></span></td><td style="position: relative"><a tabindex="-1" role="button" id="blog_body_code" href="javascript:;" class="mceButton mceButtonEnabled mce_code" onmousedown="return false;" onclick="return false;" aria-labelledby="blog_body_code_voice" title="Edit HTML Source"><span class="mceIcon mce_code"></span><span class="mceVoiceLabel mceIconOnly" style="display: none;" id="blog_body_code_voice">Edit HTML Source</span></a></td><td style="position: relative"><a tabindex="-1" role="button" id="blog_body_image" href="javascript:;" class="mceButton mceButtonEnabled mce_image" onmousedown="return false;" onclick="return false;" aria-labelledby="blog_body_image_voice" title="Insert/edit image"><span class="mceIcon mce_image"></span><span class="mceVoiceLabel mceIconOnly" style="display: none;" id="blog_body_image_voice">Insert/edit image</span></a></td><td class="mceToolbarEnd mceToolbarEndButton mceLast"><span><!-- IE --></span></td></tr></tbody></table></span></div><a href="#" accesskey="z" title="Jump to tool buttons - Alt+Q, Jump to editor - Alt-Z, Jump to element path - Alt-X" onfocus="tinyMCE.getInstanceById('blog_body').focus();"><!-- IE --></a></td></tr><tr class="mceLast"><td class="mceIframeContainer mceFirst mceLast"><iframe style="width: 100%; height: 202px;" title="{#aria.rich_text_area}. Press ALT F10 for toolbar. Press ALT 0 for help." allowtransparency="true" src='javascript:""' id="blog_body_ifr" frameborder="0"></iframe></td></tr></tbody>
<h2><?php echo $this->translate('Edit Post');?></h2>
<?php echo $this->form->render($this) ?>
