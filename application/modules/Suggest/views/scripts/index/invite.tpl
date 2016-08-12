<?php foreach ($this->friends as $friend): ?>
    <tr>
        <td><?php if ($friend['storage_path']):
                echo '<img src="' . $friend['storage_path'] . '"  class="thumb_icon item_photo_user"/>';
            else:
                echo '<img src="application/modules/User/externals/images/nophoto_user_thumb_icon.png" alt="" class="thumb_icon item_photo_user"/>';
            endif;?>
        </td>
        <td><?php echo $this->htmlLink(
                array('route' => 'user_profile', 'reset' => true, 'id' => $friend['user_id']),
                $friend['displayname'])?>
        </td>
        <td><?php if ($friend['resource_id']):?>
                <span><?php echo $this->translate('liked')?></span>
            <?php else:?>
                <button class="he-btn he-btn-sm suggest-invite-button" data-id="<?php echo $friend['user_id'] ?>" onclick="HESuggest.invite(this)"><?php echo  $this->translate('invite') ?></button>
            <?php endif?>
        </td>
    </tr>
<?php endforeach; ?>