<div id='profile_options'>
  <?php
    // Render the menu
    echo $this->navigation()
      ->menu()
      ->setContainer($this->menuNavigation)
      ->setPartial(array('_navIcons.tpl', 'core'))
      ->render();
  ?>
</div>