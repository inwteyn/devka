


<?php if (empty($this->stream['data'])):?>

  <?php
    if ($this->viewall){
      echo '<script type="text/javascript">Wall.runonce.add(function (){ Wall.dialog.message(en4.core.language.translate("WALL_STREAM_EMPTY_VIEWALL"), 2); });</script>';
      return ;
    }
  ?>

  <li>
    <div class="tip">
      <span>
        <?php echo $this->translate('WALL_STREAM_EMPTY')?>
      </span>
    </div>
  </li>

<?php return ; endif ;?>


<?php foreach ($this->stream['data'] as $action):?>

<li>

<?php

  $profile_url = "http://www.facebook.com/profile.php?id=" . $action['from']['id'];
  $matches = explode("_", $action['id']);
  $post_id = array_pop($matches);
  $post_url = "http://www.facebook.com/{$action['from']['id']}/posts/$post_id";

?>

  <div class="item_photo">
    <a href="<?php echo $profile_url?>"><img src="https://graph.facebook.com/<?php echo $action['from']['id']?>/picture" alt=""/></a>
  </div>
  <div class="item_body">
    <div class="item_title">
      <span class="name">
        <a href="<?php echo $profile_url?>"><?php echo $action['from']['name']?></a>
      </span>

    </div>
    <div class="item_text">

      <div class="body">
        <?php if (!empty($action['message'])):?>
          <?php echo $action['message'];?>
        <?php endif;?>
      </div>

      <?php if ((!empty($action['picture'])) || (!empty($action['name']) || !empty($action['caption']) || !empty($action['description']))):?>

        <div class="attachment">

          <div class="media">

            <?php if (!empty($action['picture'])):?>

              <div class="media_photo">
                <a href="<?php echo $action['link']?>" rel="nofollow"><img src="<?php echo $action['picture']?>" alt="<?php echo isset($action['name'])?$action['name']:''?>"/></a>
              </div>

            <?php endif;?>

            <?php if (!empty($action['name']) || !empty($action['caption']) || !empty($action['description'])):?>
              <div class="media_content">
                <?php if (!empty($action['name'])):?>
                  <div class="name"><a href="<?php echo $post_url?>" rel="nofollow"><?php echo $action['name']?></a></div>
                <?php endif;?>
                <?php if (!empty($action['caption'])):?>
                  <div class="caption"><a href="<?php echo $post_url?>" rel="nofollow"><?php echo $action['caption']?></a></div>
                <?php endif;?>
                <?php if (!empty($action['description'])):?>
                  <div class="description"><?php echo $action['description']?></div>
                <?php endif;?>
              </div>
            <?php endif;?>

          </div>

        </div>
      
      <?php endif;?>


    </div>
    <div class="item_line">

      <div class="item_icon" <?php if (!empty($action['icon'])):?>style="background-image: url('<?php echo $action['icon']?>');"<?php endif;?>></div>
      <div class="item_date">
        <?php echo $this->timestamp($action['updated_time']);?>
      </div>

      <ul class="item_options">

        <?php if ((!empty($action['comments']) && $action['comments']['count']) || (!empty($action['likes']) && $action['likes']['count'])):?>

          <li>
            <a href="<?php echo $post_url?>" class="count_container">
              <?php if (!empty($action['comments']) && $action['comments']['count']):?>
                <span class="count_comments"><?php echo $action['comments']['count']?></span>
              <?php endif;?>
              <?php if (!empty($action['likes']) && $action['likes']['count']):?>
                <span class="count_likes"><?php echo $action['likes']['count']?></span>
              <?php endif;?>
            </a>
          </li>

        <?php endif;?>


        <?php if (!empty($action['actions'])):?>
          <?php
            $counter = 0;
            $count = count($action['actions'])-1;
          ?>
          <?php foreach ($action['actions'] as $link):?>

            <li>
              <a href="<?php echo $link['link']?>"><?php echo $link['name']?></a>
            </li>

            <?php if ($counter < $count):?>
              <li class="<?php echo $counter?>=<?php echo $count?>">&middot;</li>
            <?php endif;?>

          <?php $counter++; endforeach;?>

        <?php endif;?>

      </ul>

    </div>
  </div>

</li>

<?php endforeach;?>



<?php if( empty($this->stream['data']) ): ?>
<li class="utility-empty" style="display: none;">
  <div class="tip">
    <span>
      <?php
        if ($this->viewall){
          echo $this->translate("WALL_STREAM_EMPTY_VIEWALL");
        } else {
          echo $this->translate("WALL_STREAM_EMPTY");
        }
      ?>
    </span>
  </div>
</li>
<?php endif;?>

<li class="utility-viewall">
  <div class="pagination">
    <a href="javascript:void(0);" rev="item_<?php echo $this->next?>"><?php echo $this->translate('View More')?></a>
  </div>
  <div class="loader" style="display: none;">
    <div class="icon"></div>
    <div class="text">
      <?php echo $this->translate('Loading ...')?>
    </div>
  </div>
</li>

