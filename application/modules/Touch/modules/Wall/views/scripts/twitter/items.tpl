<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-07-02 17:53 michael $
 * @author     Michael
 */
?>

<?php if (empty($this->stream)):?>

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


<?php foreach ($this->stream as $action):?>

  <li>

    <div class="item_photo">
      <a href=""><img src="<?php echo $action['user']['profile_image_url']?>" alt=""/></a>
    </div>

    <div class="item_body">
      <div class="item_title">
        <span class="screen_name"><?php echo $action['user']['screen_name'];?></span>
        <span class="name"><?php echo $action['user']['name'];?></span>
      </div>
      <div class="item_text">

        <?php

        $text = $action['text'];
        $text = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\" target=\"_blank\" rel='nofollow'>\\2</a>", $text);
        $text = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\" target=\"_blank\" rel='nofollow'>\\2</a>", $text);
        $text = preg_replace("/@(\w+)/", "<a href=\"http://www.twitter.com/\\1\" target=\"_blank\" rel='nofollow'>@\\1</a>", $text);
        $text = preg_replace("/#(\w+)/", "<a href=\"http://search.twitter.com/search?q=\\1\" target=\"_blank\" rel='nofollow'>#\\1</a>", $text);
        echo $text;
?>

      </div>
      <div class="item_line">

        <div class="wall_item_icon"></div>
        <div class="item_date">
          <?php echo $this->timestamp($action['created_at']);?>
        </div>

        <div class="item_options">
          <a href="http://twitter.com/intent/tweet?in_reply_to=<?php echo $action['id_str']?> " class="tweet">Reply</a>
          <a href="http://twitter.com/intent/retweet?tweet_id=<?php echo $action['id_str']?>" class="retweet">Retweet</a>
          <a href="http://twitter.com/intent/favorite?tweet_id=<?php echo $action['id_str']?>" class="favorite">Favorite</a>
        </div>

      </div>
    </div>

  </li>

<?php endforeach;?>


<?php if( empty($this->stream) ): ?>
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

<?php if ($this->show_viewall):?>
	<li class="utility-viewall">
	  <div class="pagination">
		<a href="javascript:void(0);" rev="item_<?php echo $this->next?>"><?php echo $this->translate('View More')?></a>
	  </div>
	  <div class="loader" style="display: none;">
		<div class="wall_icon"></div>
		<div class="text">
		  <?php echo $this->translate('Loading ...')?>
		</div>
	  </div>
	</li>
<?php endif;?>

