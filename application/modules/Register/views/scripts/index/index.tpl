<div>
  <?php if ($this->add_friend): ?>
    <div>
      <?php echo $this->htmlLink($this->url(array('action' => 'add-friends'), 'register_url', true), $this->translate('Add Friends'),
        array('style' => 'font-size: 15px; color: #ff20c4; font-weight: bold')
      )?>
    </div>
  <?php endif; ?>

  <div>
    <?php echo $this->htmlLink($this->url(array('action' => 'add-users'), 'register_url', true), $this->translate('Add Users'),
      array('style' => 'font-size: 15px; color: #18a2ff; font-weight: bold')
    )?>
    <br/>
    <?php echo $this->htmlLink($this->url(array('action' => 'add-blogs'), 'register_url', true), $this->translate('Add Blogs'),
      array('style' => 'font-size: 15px; color: #18a2ff; font-weight: bold')
      )?>
  </div>
</div>