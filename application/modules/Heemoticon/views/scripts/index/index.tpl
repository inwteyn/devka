<?php
if($this->errorLicense){
  echo 'Your license invalid!';
  die;
}
?>
<div class="wall_arrow_container"><div class="wall_arrow" <?php if($this->typeLoad && $this->typeLoad=='bottom'){ ?> style="top: 339px;box-shadow: 3px 5px 10px -1px rgba(0, 0, 0, 0.5);"<?php }?>></div></div>

<div id="main_content_smiles" >
  <div class="main_contaner_smiles" >
    <div class="header-smiles-contaner" style="width: 305px;">
      <a href="javascript:void(0)" style="float: left;<?php if($this->count<4) echo 'opacity: 0.3;cursor: default'; else echo 'opacity: 0.3;'?>" class="smile_left_scroll" onclick="window.heemotion.scrollleftSmiles()">
        <i class="hei hei-chevron-left"></i>
      </a>
      <div class="smile_tabs_scroll">

        <div class="tabs_smiles" >
          <a href="javascript:void(0)" class="heemoticon_smiles active"  onclick="window.heemotion.showEmoticonById(-1,this)" title="<?php echo $this->translate('Wall smiles')?>"><?php echo $this->standart[0]['html'] ?></a>
          <?php foreach ($this->titles as $collect): ?>
            <a href="javascript:void(0)" class="heemoticon_smiles" onclick="window.heemotion.showEmoticonById(<?php echo $collect['collection_id'] ?>,this)" title="<?php echo $this->translate($collect['name'])?>">
              <img src="<?php echo $collect['cover'] ?>"/>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
      <a href="javascript:void(0)" class="smile_right_scroll" onclick="window.heemotion.scrollRightSmiles()" style="<?php if($this->count<4) echo 'opacity: 0.3;cursor: default'?>">
        <i class="hei hei-chevron-right"></i>
      </a>
      <a href="javascript:void(0)" onclick="window.heemotion.addNewSmiles()"  class="add-icon-smiles">
        <i class="hei hei-plus"></i>
      </a>
    </div>

    <div class="smiles_contaner" id="smiles_colection_standart">

      <div class="wall_data_comment" id="standart">
        <ul>
          <?php foreach ($this->standart as $smile): ?>
            <li style=" float: left">
              <a title="<?php echo $smile['title'] ?>" href="javascript:void(0)" rev="<?php echo $smile['index_tag'] ?>"
                 class="smiles_standart">
                <?php echo $smile['html'] ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
    <?php $i = 0;
    foreach ($this->emoticons as $k => $smiles):?>
      <div class="smiles_contaner" id="smiles_colection_<?php echo $smiles['collection_id'] ?>" style="display: none">
        <div class="wall_data_comment" id="standart">
          <ul>
            <?php foreach ($smiles['smiles'] as $k => $smile): ?>
              <li style=" float: left">
                <a title="<?php echo $smile['name'] ?>" href="javascript:void(0)" id="smiles_NEW_smile_<?php echo $smile['id']?>" rev="<?php echo $smile['name'] ?>" data-id='<?php echo $smile['id']?>' class="smiles_NEW" style=" ">
                  <img src="<?php echo $smile['url'] ?>" style="opacity: 0"/></a>
                </a>
              </li>
              <style>
                #smiles_NEW_smile_<?php echo $smile['id']?>:hover{
                  background-image: url("<?php echo $smile['url']?>");
                }
                #smiles_NEW_smile_<?php echo $smile['id']?>{
                  background-image: url(<?php echo $smile['url_no_animaticon'] ?>);
                  background-repeat: no-repeat;
                  background-size:contain;
                  background-position:center ;
                  margin-right: 2px;
                }
              </style>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>

    <?php endforeach; ?>


  </div>
</div>
