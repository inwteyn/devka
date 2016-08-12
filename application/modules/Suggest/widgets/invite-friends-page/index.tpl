<?php echo $this->likes_count; ?> <span><?php echo $this->translate("likes"); ?></span>
<hr/>
<div>
    <?php $famount = count($this->friends); $counter = 0; ?>
    <?php foreach ($this->friends as $friend): ?>
        <?php if ($counter == 2) {
            $famount -= 2;
            if ($famount > 0) {
                echo $this->translate('and') . ' ';
                echo $famount . ' ';
                if ($famount == 1)
                    echo $this->translate('other like this');
                else
                    echo $this->translate('others like this');
            } else {
                echo $this->translate('like this');
            }
            break;
        }
        echo $this->htmlLink(
            array('route' => 'user_profile', 'reset' => true, 'id' => $friend['user_id']),
            $friend['displayname']);
        $counter++;
        ?>

    <?php endforeach; ?>
    <?php if ($counter == 1)echo $this->translate('likes this');?>
    <?php $counter = 0; ?>
    <div class="clr">
        <?php foreach ($this->friends as $friend): ?>
            <?php if ($counter++ < 5): ?>
                <?php if ($friend['storage_path']): ?>
                    <img src="<?php echo $friend['storage_path']; ?>" class="thumb_icon item_photo_user"/>
                <?php else: ?>
                    <img src="application/modules/User/externals/images/nophoto_user_thumb_icon.png" alt=""
                         class="thumb_icon item_photo_user"/>
                <?php endif; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>


</div>
<input type="text" name="user" id="suggest-search-input"
       placeholder="<?php echo $this->translate('Invite your friends to like') . ' ' . $this->pageName; ?>"/>
<table id="suggest-friend-list">
    <tbody></tbody>
</table>
<div class="clr">
    <?php echo $this->htmlLink(
        array(
            'route' => 'suggest_invite_friends',
            'user_id' => $this->viewer->getIdentity(),
            'page_id' => $this->subject->getIdentity(),
            'page_name' => $this->subject->getTitle()
        ), $this->translate('Invite your friends to like this Page'),
        array('id' => 'suggest-invite-friends', 'data-offset' => 0));?>
</div>
