<ul class="highlight_vetrical" id="horizontal_highlights">
  <?php foreach ($this->lastHighlights as $highlight): ?>
    <li class="highlight_horizontal_list">
         <?php echo '<span style="background-image: url(\'' . (($highlight->getOwner()->getPhotoUrl() != '')?$highlight->getOwner()->getPhotoUrl('thumb.profile'):$this->layout()->staticBaseUrl . 'application/modules/User/externals/images/nophoto_user_thumb_profile.png') . '\')"></span>';?>
         <div class="profile_url">
           <?php echo $this->htmlLink($highlight->getOwner()->getHref(), $highlight->getOwner()->getTitle())?>
           <div class="additional_info">
             <p title="<?php echo Engine_Api::_()->highlights()->getUserInfo($highlight->getOwner()->getIdentity());?>"><?php echo Engine_Api::_()->highlights()->getUserInfo($highlight->getOwner()->getIdentity());?></p>
           </div>
         </div>
       </li>
  <?php endforeach;?>
</ul>
<?php if(Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('credit') && $this->canHighlight):?>
<?php echo $this->htmlLink(array('route' => 'highlight_general', 'action' => 'add'),
  $this->translate('Add Me Here'),
  array('class' => 'smoothbox add-me-here', 'style' => 'top: -10px')) ?>
  <div style="clear: both"></div>
<?php endif;?>