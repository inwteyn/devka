<div class="headline">
  <h2>
    <?php echo $this->translate('Videos');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>

<div class='layout_right'>
  <?php echo $this->form->render($this); ?>
</div>

<div class='layout_middle'>
      <div class="tip">
            <span>
                  <?php echo $this->avpInformation(); ?>
            </span>
      </div>
      <?php if ($this->message == 1): ?>
            <div class="notice">
                  <span>
                  <?php echo $this->translate('The video you uploaded will be processed by our server and will be available soon. Meanwhile you can continue browsing our website.'); ?>
                  </span>
            </div>
      <?php elseif ($this->message == 2): ?>
            <div class="notice">
                  <span>
                  <?php echo $this->translate('The video was successfully imported.'); ?>
                  </span>
            </div>
      <?php endif; ?>

      <?php if ($this->paginator->getTotalItemCount() > 0): ?>

      <ul class="avp_browse">
        <?php foreach( $this->paginator as $item ): ?>
          <li>

            <div class="avp_thumb_wrapper">
                  <span class="avp_tool_bar">
                        <?php if ($item->status == 1): ?>
                              <?php echo $this->htmlImage('application/modules/Avp/externals/images/avp_edit_icon.png', $this->translate('Edit Video'), array('class' => 'avp_tool_icons')) ?>
                              <?php echo $this->htmlImage('application/modules/Avp/externals/images/avp_delete_icon.png', $this->translate('Delete Video'), array('class' => 'avp_tool_icons')) ?>
                        <?php elseif ($item->status == 2): ?>
                              <div><?php echo $this->translate('In process'); ?></div>
                        <?php elseif ($item->status == 3): ?>
                              <div><?php echo $this->translate('Failed'); ?></div>
                              <?php echo $this->htmlImage('application/modules/Avp/externals/images/avp_delete_icon.png', $this->translate('Delete Video'), array('class' => 'avp_tool_icons')) ?>
                        <?php else: ?>
                              <div><?php echo $this->translate('Waiting to be processed'); ?></div>
                        <?php endif; ?>
                  </span>
                  <span class="avp_tools">
                        <?php if ($item->status == 1): ?>
                              <a style="float: left;" href="<?php echo $this->url(array('action' => 'edit', 'id' => $item->video_id), 'avp_general', true) ?>"><?php echo $this->htmlImage('application/modules/Avp/externals/images/avp_edit_icon.png', $this->translate('Edit Video'), array('class' => 'avp_tool_icons')) ?></a>
                              <a style="float: left;" class="smoothbox" href="<?php echo $this->url(array('action' => 'delete', 'id' => $item->video_id, 'format' => 'smoothbox'), 'avp_general', true) ?>"><?php echo $this->htmlImage('application/modules/Avp/externals/images/avp_delete_icon.png', $this->translate('Delete Video'), array('class' => 'avp_tool_icons')) ?></a>
                        <?php elseif ($item->status == 2): ?>
                              <div><?php echo $this->translate('In process'); ?></div>
                        <?php elseif ($item->status == 3): ?>
                              <div><?php echo $this->translate('Failed'); ?></div>
                              <a style="float: left;" class="smoothbox" href="<?php echo $this->url(array('action' => 'delete', 'id' => $item->video_id, 'format' => 'smoothbox'), 'avp_general', true) ?>"><?php echo $this->htmlImage('application/modules/Avp/externals/images/avp_delete_icon.png', $this->translate('Delete Video'), array('class' => 'avp_tool_icons')) ?></a>
                        <?php else: ?>
                              <div><?php echo $this->translate('Waiting to be processed'); ?></div>
                        <?php endif; ?>
                  </span>
                  <?php if ($item->status == 1 && $item->duration): ?>
                  <span class="avp_length">
                        <?php
                              if( $item->duration>3600 ) $duration = gmdate("H:i:s", $item->duration); else $duration = gmdate("i:s", $item->duration);
                              if ($duration[0] =='0') $duration = substr($duration,1);
                              if (count(explode(":", $duration)) > 2 && substr($duration, 0, 2) == "0:") $duration = substr($duration ,2);
                              echo $duration;
                        ?>
                  </span>
                  <?php endif;?>
                  <?php
                    echo $item->getThumbnail();
                  ?>
            </div>
            <a class="avp_title" href='<?php echo ($item->status == 1 ? $item->getHref() : '#'); ?>'>
                  <?php
                        if (mb_strlen($item->getTitle(), 'UTF-8') > 43):
                              echo trim(mb_substr($item->getTitle(), 0, 40, 'UTF-8'))."...";
                        else:
                              echo $item->getTitle();
                        endif;
                  ?>
            </a>
            <div class="avp_stats">
              <span class="avp_views"><?php echo $item->view_count;?> <?php echo $this->translate('views');?></span>
              <?php echo $this->avpRating($item->rating); ?>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
      <?php else: ?>
        <div class="tip">
          <span>
          <?php
          if ($this->is_search):
              echo $this->translate("There were no videos found with the search criteria.");
          else:
              $viewer = Engine_Api::_()->user()->getViewer();
              echo $this->translate('You haven\'t posted any videos yet.')." ";
              if (Engine_Api::_()->authorization()->isAllowed('avp_video', $viewer, 'import') && Engine_Api::_()->authorization()->isAllowed('avp_video', $viewer, 'upload')):
                  echo $this->translate('%1$sUpload%2$s or %3$simport%4$s your first video now.', '<a href="'.$this->url(array('action' => 'upload'), "avp_general").'">', '</a>', '<a href="'.$this->url(array('action' => 'import'), "avp_general").'">', '</a>');
              elseif (Engine_Api::_()->authorization()->isAllowed('avp_video', $viewer, 'import')):
                  echo $this->translate('%1$sImport%2$s your first video now.', '<a href="'.$this->url(array('action' => 'import'), "avp_general").'">', '</a>');
              elseif (Engine_Api::_()->authorization()->isAllowed('avp_video', $viewer, 'upload')):
                  echo $this->translate('%1$sUpload%2$s your first video now.', '<a href="'.$this->url(array('action' => 'upload'), "avp_general").'">', '</a>');
              endif;
          endif;
          ?>
          </span>
        </div>
      <?php endif; ?>
      <?php echo $this->paginationControl($this->paginator); ?>
</div>