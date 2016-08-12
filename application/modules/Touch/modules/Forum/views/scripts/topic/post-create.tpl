<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: post-create.tpl 8221 2011-01-15 00:24:02Z john $
 * @author     Sami
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
        &#187; <?php echo $this->htmlLink(array('route'=>'forum_forum', 'forum_id'=>$this->forum->getIdentity()), $this->forum->getTitle(), array('class' => 'touchajax'));?>
        &#187; <?php echo $this->htmlLink(array('route'=>'forum_topic', 'topic_id'=>$this->topic->getIdentity()), $this->topic->getTitle(), array('class' => 'touchajax'));?>
        &#187 <?php echo $this->translate('Post Reply');?>
      </h3>
    </div>
  </div>
  <div style="height: 10px"></div>

  <?php echo $this->form->render($this) ?>
</div>
