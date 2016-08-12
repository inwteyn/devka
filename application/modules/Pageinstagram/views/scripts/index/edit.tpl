<?php
if($this->tag){
    echo "<ul id=\"photos_instagram\">";
    foreach ($this->tag as $data) {
        echo "<li id='img_user_id_".$data->user_id."' style='position: relative;width: 195px;margin: 5px;float: left;'><span class='delete_img' onclick='instagram.delete_photos({$data->instagram_id},{$this->page_id});'><i class='hei hei-trash-o hei-3x red' ></i></span><img style='width: 200px;' src=\"{$data->href}\"></li>";
    }?>
<?php
        echo "</ul>";
    }
?>
