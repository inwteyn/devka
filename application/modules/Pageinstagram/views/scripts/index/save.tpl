<?php
if($this->tag){
    echo "<ul id=\"photos_instagram\">";
    foreach ($this->tag as $data) {
        echo "<li id='img_user_id_".$data->user_id."' class='photo_viewer' style='width: 195px;margin: 5px;float: left;'><img style='width: 200px;' src=\"{$data->href}\">
        <input type='hidden' id='link' value='".$data->link."'>
        <input type='hidden' id='profile_picture' value='".$data->user_img."'>
        <input type='hidden' id='username' value='".$data->user_name."'>
        <input type='hidden' id='likes' value='".$data->count_like."'>
        <input type='hidden' id='comments' value='".$data->count_comment."'>
        <input type='hidden' id='caption' value='".$data->description."'>
        </li>";
    }?>
<?php
        echo "</ul>";
    }
?>
