<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: topic-create.tpl 8918 2011-05-05 20:22:53Z shaun $
 * @author     John
 */
?>

<script type="text/javascript">
function showUploader()
{
  $('photo').style.display = 'block';
  $('photo-label').style.display = 'none';
}
</script>

<div class="layout_content">
  <div class="touch-navigation">
    <div class="navigation-header">
      <h3>
      <?php echo $this->htmlLink(array('route'=>'forum_general'), $this->translate("Forums"), array('class' => 'touchajax'));?>
        &#187; <?php echo $this->htmlLink(array('route'=>'forum_forum', 'forum_id'=>$this->forum->getIdentity()), $this->translate($this->forum->getTitle()), array('class' => 'touchajax'));?>
        &#187 <?php echo $this->translate('Post Topic');?>
      </h3>
    </div>
  </div>
  <div style="height: 10px"></div>

  <?php echo $this->form->render($this) ?>
</div>
