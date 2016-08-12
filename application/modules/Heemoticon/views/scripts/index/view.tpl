<div id="main_content_smiles_view" >



  <div class="main_contaner_smiles">
    <div class="tabs_smiles">

     <a href="javascript:void(0)" onclick="window.heemotion.addNewSmiles()" style="float: left;">

       <i class="hei hei-chevron-left" ></i>
     </a>
      <span class="return_to_all_collection"  onclick="window.heemotion.addNewSmiles()" ><?php echo $this->translate('Sticker Store');?></span>
      <div onclick="window.heemotion.hideSelectSmile()" class="close_heemoticon_contaner">
        <i class="hei hei-times" ></i>
      </div>

    </div>
    <div class="sticker-description">
      <div class="stickers-description-photo">
        <img class="sticker-view-photo" src="<?php echo $this->mainphoto?>" style="height: 150px;">
      </div>
      <div class="stickers-description-text">
    <div class="collection_title_from_view"> <?php  echo $this->emoticons[0]['colection_name'];?></div>
     <?php if($this->emoticons[0]['author']){ ?>   <div class="heemoticon_author" style="margin: 5px"> <?php  echo $this->translate('By').' - '. $this->emoticons[0]['author'];?></div><?php } ?>
    <div class="collection_desk_from_view"> <?php  echo $this->emoticons[0]['description'];?></div>
    <?php if(in_array($this->viewer->level_id,$this->privacy) || in_array(0,$this->privacy)):?>
      <?php  if(!$this->purchased->getUsed($this->emoticons[0]['collection_id'],$this->viewer->getIdentity())):?>

        <button  onclick="window.heemotion.addCollectionSticers(<?php echo $this->emoticons[0]['collection_id']?>,this)">
          <?php echo $this->translate("Add"); ?>
        </button>
      <?php else:?>
        <button  onclick="window.heemotion.removeCollectionSticers(<?php echo $this->emoticons[0]['collection_id']?>,this)">
          <?php echo $this->translate("Remove"); ?>
        </button>
      <?php endif;?>
    <?php endif;?>
      </div>
      </div>
      <div class="smiles_contaner" id="smiles_colection" >
        <div class="" id="standart" >
          <ul>
            <?php if(in_array($this->viewer->level_id,$this->privacy) || in_array(0,$this->privacy)){
              if(!$this->photoSmiles){
                foreach($this->emoticons as $k => $smile){ ?>
                  <li style=" float: left">
                    <a title="<?php echo $smile['name']?>" href="javascript:void(0)" rev="<?php echo $smile['name']?>" class="smiles_NEW">
                      <img src="<?php echo $smile['url']?>" />
                    </a>
                  </li>
                <?php }
              }else{
                ?>
                <img class="sticker-view-photo" src="<?php echo $this->photoSmiles?>" >
              <?php
              }
            }else{
              echo '<div style="margin: 10px; font-size: 20px">'.$this->translate('Your level does not permit to see this collection.').'</div>';
            } ?>
          </ul>
        </div>
      </div>




  </div>
</div>
