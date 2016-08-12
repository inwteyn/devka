<div class="edit_contacts_offer">
  <?php
    echo $this->form->render();
  ?>
</div>
<div class="offers_navigation_editor tabs">
  <?php echo $this->navigation()->menu()->setContainer($this->navigation_edit)->setPartial(array('_navIcons.tpl', 'core'))->render(); ?>
</div>