<div class="contaner_smile_store">
  <div class="smile_store_header"
       style="background-image:url(application/modules/Heemoticon/externals/images/default.png);">
    <div class="header_content_heemoticon">
      <div class="header_title_heemoticon"><i
          class="hei hei-shopping-cart"></i><?php echo $this->translate("Sticker Store") ?></div>
      <div
        class="header_description_heemoticon"><?php echo $this->translate("Find new stickers to send to friends") ?></div>
    </div>
  </div>
  <div onclick="window.heemotion.hideSelectSmile()" class="close_heemoticon_contaner">
    <i class="hei hei-times"></i>
  </div>
  <div class="smile_store_content">

    <?php
    foreach ($this->emoticons as $item) {
      if ($item['collection_id']) {
        ?>
        <div class="store_smile_item">

          <?php $price = $this->collections->getPrice($item['collection_id']); ?>
          <?php $collectionTypeStatus = (!$price == 0) && ($this->creditModuleStatus); ?>
          <?php $buyed = $this->buyeds->getBuyed($item['collection_id'], $this->viewer_user->getIdentity()); ?>

          <?php if (!$buyed || !$collectionTypeStatus) { ?>
            <div class="collection_price">
              <span><?php echo($collectionTypeStatus ? $price . $this->translate(" credits") : $this->translate("Free")); ?></span>
            </div>
          <?php } ?>

          <div class="sticers_icons"
            <?php if (!$buyed && $collectionTypeStatus) { ?>
              onclick="window.heemotion.viewBuyEmoticonsDetails(<?php echo $item['collection_id'] ?>)"
            <?php } else { ?>
              onclick="window.heemotion.viewEmoticonsDetails(<?php echo $item['collection_id'] ?>)"
            <?php }; ?>
            >
            <?php
            $i = 0;

            foreach ($item['smiles'] as $smile) {
              if ($i == 3) continue;
              ?>
              <div style="background-image: url(<?php echo $smile['url'] ?>);" class="emoticon-icons"></div>
              <?php
              $i++;
            }
            ?>
          </div>
          <div class="emoticon-title">
            <?php echo $item['name']; ?>
            <div class="heemoticon_author">
              <?php echo $item['author']; ?>
            </div>
          </div>

          <?php if (in_array($this->viewer_user->level_id, $item['privacy']) || in_array(0, $item['privacy'])) { ?>
            <?php if (!$item['used']): ?>
              <?php if (!$buyed && $collectionTypeStatus) { ?>

                <button onclick="window.heemotion.viewBuyEmoticonsDetails(<?php echo $item['collection_id'] ?>,this)">
                  <?php echo $this->translate("Buy"); ?>
                </button>

              <?php } else { ?>

                <button onclick="window.heemotion.addCollectionSticers(<?php echo $item['collection_id'] ?>,this)">
                  <?php echo $this->translate("Add"); ?>
                </button>

              <?php }; ?>
            <?php else: ?>

              <button onclick="window.heemotion.removeCollectionSticers(<?php echo $item['collection_id'] ?>, this)">
                <?php echo $this->translate("Remove"); ?>
              </button>

            <?php endif; ?>
          <?php } else { ?>

            <?php if (!$buyed && $collectionTypeStatus) { ?>
              <button onclick="window.heemotion.viewEmoticonsDetails(<?php echo $item['collection_id'] ?>,this)">
                <?php echo $this->translate("Buy"); ?>
              </button>
            <?php } else { ?>
              <button onclick="window.heemotion.viewEmoticonsDetails(<?php echo $item['collection_id'] ?>,this)">
                <?php echo $this->translate("Add"); ?>
              </button>
            <?php }; ?>

          <?php } ?>
        </div>
      <?php
      }
    }
    ?>

  </div>
</div>
