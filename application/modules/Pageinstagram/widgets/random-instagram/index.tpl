<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Pageinstagram/externals/scripts/instagram.js');
?>

<!--MODAL VIEW PHOTO-->
<div id="bg_modal" style="display: none;">
</div>

<div id="modal" style="display: none;">
    <div class="left_with_photo">
        <div class="header_photo">

        </div>
        <div class="after_head">
            <div class="inner_block">
                <span class="like_instagram"><i class="hei hei-heart-o hei-2x"></i><span class="like_instagram_count"></span></span>
                <span class="comment_instagram"><i class="hei hei-comments-o hei-2x"></i><span class="comment_instagram_count"></span></span>
            </div>
        </div>
        <div class="image_instagram">
            <div class="position_img_instagram">
                <img  class="src_instagram" src="">
            </div>
        </div>
    </div>
    <div class="right">

        <div class="block_info">
            <div class="user_info_instagram">
                <img  class="profile_img" src=""><span class="name_profile"><a class="link" href="" target="_blank"></a></span>

            </div>
            <div class="x_close">x</div>
        </div>

        <div class="description_instagram"></div>
    </div>
</div>
<!--END MODAL VIEW PHOTO-->




    <div id="content_instagram">
        <?php

        if($this->paginator->getTotalItemCount() > 0 ){
            echo "<ul id=\"photos_instagram\">";
            foreach ($this->paginator as $data) {
                echo "<li id='img_user_id_".$data->user_id."' class='photo_viewer' style='width: 195px;margin: 5px;float: left;'><img style='width: 200px;' src=\"{$data->href}\">
               <input type='hidden' id='link' value='".$data->link."'>
                <input type='hidden' id='profile_picture' value='".$data->user_img."'>
                <input type='hidden' id='username' value='".$data->user_name."'>
                <input type='hidden' id='likes' value='".$data->count_like."'>
                <input type='hidden' id='comments' value='".$data->count_comment."'>
                <input type='hidden' id='caption' value='".$data->description."'>
                <input type='hidden' class='instagram_id' value='".$data->instagram_id."'>
                </li>";
            }?>
            <?php
            echo "</ul>";

        }
        ?>
    </div>
<script type="text/javascript">
    $$('.photo_viewer').removeEvents().addEvent('click',function(e){
        $$('.link').set('href',$(this).getElementById('link').get('value'));
        $$('.link').set('html',$(this).getElementById('username').get('value'));
        $$('.description_instagram').set('html',$(this).getElementById('caption').get('value'));
        $$('.like_instagram_count').set('html',$(this).getElementById('likes').get('value'));
        $$('.comment_instagram_count').set('html',$(this).getElementById('comments').get('value'));
        $$('.profile_img').set('src',$(this).getElementById('profile_picture').get('value'));
        $$('.src_instagram').set('src',e.target.get('src'));
        $$('#bg_modal')[0].inject($$('body')[0],'top');
        $$('#modal')[0].inject($$('body')[0],'top');
        $('bg_modal').show();
        $('modal').show();
    });

    $$('.x_close').removeEvents().addEvent('click',function(e){
        $('bg_modal').hide();
        $('modal').hide();
    });
</script>

<?php if($this->paginator->getTotalItemCount() > 0){ ?>
    <div class="more_photos_show_for_users" onclick="instagram.more_photos_show_for_users_random(<?php echo $this->page_number;?>,1);"><span class="more_span"><?php echo $this->translate('Page_More'); ?></span></div>
<?php } ?>