<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: privacy.tpl 8344 2011-01-29 07:46:14Z john $
 * @author     Steve
 */
?>


<script type="text/javascript">
<!--

var seek = setInterval(
  function(){
    if($('blockedUserList')){
      clearInterval(seek);
      $$('#blockedUserList ul')[0].inject($('blockList-element'));
    }
  }, 500
);
// -->
</script>
<?php if(!$this->posted && count($this->navigation) > 0 ): ?>
<h3 class="settings_headline">
  <?php echo $this->translate('My Settings');?>
</h3>
<?php
		// Render the menu
		echo $this->navigation()
->menu()
->setContainer($this->navigation)
->setPartial(array('navigation/index.tpl', 'touch'))
->render();
?>
<?php endif; ?>
<div>
  <div id="navigation_content">
    <?php echo $this->form->setAttrib('class', 'global_form touchform')->render($this) ?>

    <div id="blockedUserList" style="display:none;">
      <ul>
        <?php foreach ($this->blockedUsers as $user): ?>
          <?php if($user instanceof User_Model_User && $user->getIdentity()) :?>
            <li>[
              <?php echo $this->htmlLink(array('controller' => 'block', 'action' => 'remove', 'user_id' => $user->getIdentity()), 'Unblock', array('class'=>'smoothbox')) ?>
              ] <?php echo $user->getTitle() ?></li>
          <?php endif;?>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</div>
