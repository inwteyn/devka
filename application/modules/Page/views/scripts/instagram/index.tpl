<?php
if( $this->paginator->getTotalItemCount() > 0 ){
    echo "<ul id=\"photos_instagram\">";


    foreach ($this->paginator as $data) {

        echo "<li id='img_user_id_".$data->user->id."'  class='no_check'><span class='check_img'><i class='hei hei-check-circle hei-3x green'></i></span><img style='width: 200px;' src=\"{$data->images->standard_resolution->url}\">
        <input type='hidden' id='link' value='".$data->link."'>
        <input type='hidden' id='profile_picture' value='".$data->user->profile_picture."'>
        <input type='hidden' id='username' value='".$data->user->username."'>
        <input type='hidden' id='likes' value='".$data->likes->count."'>
        <input type='hidden' id='comments' value='".$data->comments->count."'>
        <input type='hidden' id='caption' value='".$data->caption->text."'>
        </li>";
    }?>
<?php
        echo"<input type='hidden' id='page' value='".$this->page."'>";
        echo"<input type='hidden' id='tag' value='".$this->tag."'>";
        echo "</ul>";
    }
$option = $this->paginator->getTotalItemCount()< $this->optopn;
?>
<script type="text/javascript">
    if(<?php echo $option;?>){
        $$('.more_view').hide();
    }else{
        $$('.more_view').show();
    }
</script>