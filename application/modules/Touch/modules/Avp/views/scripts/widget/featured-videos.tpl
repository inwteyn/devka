<div class="generic_layout_container layout_avp_videos_list">
    <h3><?php echo $this->translate('Featured Videos'); ?></h3>
    <ul>
      <?php foreach ($this->featured as $item): ?>
        <li>
          <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('class' => 'avp_videos_thumb')) ?>
          <div class='avp_videos_info'>
            <div class='avp_videos_title'>
              <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
            </div>
            <div class='avp_videos_date'>
              <?php echo $this->timestamp($item->creation_date) ?>
            </div>
            <div class='avp_videos_owner'>
              <?php
                $owner = $item->getOwner();
                echo $this->translate('Posted by %1$s', $this->htmlLink($owner->getHref(), $owner->getTitle()));
              ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
</div>