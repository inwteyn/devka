<?php
if( $this->paginator->getTotalItemCount() > 0 ){
    echo "<ul id=\"photos_instagram\">";
    foreach ($this->paginator as $data) {
        echo "<li id='img_user_id_".$data->user_id."' class='photo_viewer'  style='width: 195px;margin: 5px;float: left;'><img style='width: 200px;' src=\"{$data->href}\">
        <input type='hidden' id='link' value='".$data->link."'>
        <input type='hidden' id='profile_picture' value='".$data->user_img."'>
        <input type='hidden' id='username' value='".$data->user_name."'>
        <input type='hidden' id='likes' value='".$data->count_like."'>
        <input type='hidden' id='comments' value='".$data->count_comment."'>
        <input type='hidden' id='caption' value='".$data->description."'>
        </li>";
    }?>
<?php
        echo"<input type='hidden' id='page' value='".$this->page."'>";
        echo "</ul>";
    }
$option = $this->paginator->getTotalItemCount()< $this->optopn;
?>
<script type="text/javascript">
    if(<?php echo $option;?>){
        $$('.more_photos_show_for_users').hide();
    }
</script>
