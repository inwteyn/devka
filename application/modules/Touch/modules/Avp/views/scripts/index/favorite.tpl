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

<div class='layout_middle'>
      <?php if ($this->paginator->getTotalItemCount() > 0): ?>

      <ul class="avp_browse">
        <?php foreach( $this->paginator as $item ): ?>
          <li>

            <div class="avp_thumb_wrapper">
                  <?php if ($item->duration):?>
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
            <a class="avp_title" href='<?php echo $item->getHref();?>'>
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
              <span class="avp_rating_small">
              <?php if($item->rating>0):?>
                <?php for($x=1; $x<=$item->rating; $x++): ?><span class="avp_rating_star_small"></span><?php endfor; ?>
                <?php if((round($item->rating)-$item->rating)>0):?><span class="avp_rating_star_small_half"></span><?php endif; ?>
                <?php for($x=1; $x<=(5-ceil($item->rating)); $x++): ?><span class="avp_rating_star_small_disabled"></span><?php endfor; ?>
              <?php endif; ?>
              </span>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
      <?php else: ?>
        <div class="tip">
          <span>
            <?php echo $this->translate('You don\'t have any favorite videos yet.');?>
          </span>
        </div>
      <?php endif; ?>
      <?php echo $this->paginationControl($this->paginator); ?>
</div>