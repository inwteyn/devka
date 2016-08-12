<?php
if($this->tag){
    echo "<ul id=\"photos_instagram\">";
    foreach ($this->tag->data as $data) {
        echo "<li style='width: 150px; margin: 5px;float: left;'><img src=\"{$data->images->thumbnail->url}\"></li>";
    }
    echo "</ul>";
}
?>