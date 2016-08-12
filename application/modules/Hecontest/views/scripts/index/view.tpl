<?php if ($this->allowSuggest): ?>
<?php
$options = array(
    'm' => 'suggest',
    'c' => 'HESuggest.suggest',
    'l' => 'getSuggestItems',
    'nli' => 0,
    'ipp' => 30,
    'params' => array(
        'suggest_type' => 'link_hecontest_photo',
        'object_type' => 'hecontest_photo',
        'object_id' => (int)$this->photo->getIdentity(),
        'scriptpath' => 'application/modules/Suggest/views/scripts',
        'potential' => 0
    )
);
?>

<script type="text/javascript">
    HESuggest.init('<?php echo $this->url(array(
    "controller" => "index",
    "action" => "suggest",
    "object_id" => $this->photo->getIdentity(),
    "object_type" => 'hecontest_photo',
    "suggest_type" => 'link_hecontest_photo'
  ), "suggest_general"); ?>', <?php echo Zend_Json_Encoder::encode($options); ?>);
</script>
<?php endif; ?>

<div id="another-like-wrapper">
    <?php if ($this->photo->allowLike($this->viewer()->getIdentity())) {
            if (!$this->photo->isVoter($this->viewer()->getIdentity())) {
                $likeTitle = 'HECONTEST_Like';
                $class = "up";
            } else {
                $likeTitle = 'HECONTEST_Unlike';
                $class = "down";
            }?>
                <a onclick="$('hecontest-photo-like-<?php echo $this->photo->getIdentity(); ?>').click();" class="hei hei-thumbs-<?php echo $class; ?>" id="another-like">
                    <?php echo " " . $this->translate($likeTitle); ?>
                </a>
    <?php } ?>
</div>


<div id="hecontest-photo-img">
    <img src="<?php echo $this->photo->getPhotoUrl(); ?>"/>
</div>
<div id="hecontest-photo-comments">
    <?php echo $this->content()->renderWidget('core.comments'); ?>
</div>
<div id="hecontest-photo-description">
    <div style="float: left; max-width: 90%; max-height: 80px; overflow-y: auto;">
        <p>
            <?php echo $this->photo->description; ?>
        </p>
    </div>
    <div style="float: right;">
        <?php $user = $this->photo->getUser(); if($user): ?>
        <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('class' => 'hecontest-vs-hetips')); ?>
        <?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array('style' => 'display: block')); ?>
        <?php endif; ?>
    </div>
    <div class="clear"></div>
</div>
<div id="hecontest-photo-controls">
    <div class="hecontest-vote-form-photo-vote">
        <?php $likeOnClick = '';
        $likeHref = '';
        $likeTitle = '';
        ?>
        <?php if (!$this->viewer()->getIdentity()) {
            $return_url = $this->url(array('action' => 'index'), 'hecontest_general') . '#' . $this->photo->getIdentity();
            $likeHref = $this->url(array('return_url' => '64-' . base64_encode($return_url)), 'user_login');
            $likeOnClick = "document.location.href='$href'";
            $likeTitle = 'HECONTEST_Login';
        } else {
            if ($this->photo->allowLike($this->viewer()->getIdentity())) {
                if (!$this->photo->isVoter($this->viewer()->getIdentity())) {
                    $likeTitle = 'HECONTEST_Like';
                } else {
                    $likeTitle = 'HECONTEST_Unlike';
                }
                $likeHref = 'javascript://';
                $likeOnClick = "hecontestCore.vote(this, '" . $this->photo->getIdentity() . "');";
            }
        }
        ?>
    </div>
    <?php if($this->viewer()->getIdentity()): ?>
        <?php if($this->photo->allowLike($this->viewer()->getIdentity())):?>
            <a onclick="<?php echo $likeOnClick; ?>" href="<?php echo $likeHref; ?>" class="hecontest-photo-like"
               id="hecontest-photo-like-<?php echo $this->photo->getIdentity(); ?>"><?php echo $this->translate($likeTitle); ?></a>
        <?php endif; ?>
        <a onclick="hecontestViewer.comment();" class="hecontest-photo-control-btn" id="hecontest-photo-comment">Comment</a>
    <?php endif; ?>
    <?php if ($this->viewer()->getIdentity()): ?>
        <a onclick="hecontestViewer.share('<?php echo $this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'share', 'route' => 'default', 'type' => 'hecontest_photo', 'id' => $this->photo->getIdentity(), 'format' => 'smoothbox'), 'default', true) ?>');"
           class="hecontest-photo-control-btn"
           id="hecontest-photo-share"
           href="javascript://"
            >
            Share
        </a>
    <?php endif; ?>

    <?php if ($this->allowSuggest): ?>
        <a onclick="hecontestViewer.suggest();" class="hecontest-photo-control-btn"
           id="hecontest-photo-suggest">Suggest</a>
    <?php endif; ?>
</div>
<div id="hecontest-photo-voters">
    <?php if ($this->voters->getTotalItemCount()): ?>
        <div class="hecontest-vote-photo-likers">
            <h3><?php echo $this->translate("HECONTEST_People Liked This", $this->voters->getTotalItemCount()); ?></h3>
            <ul class="hecontest-vote-photo-likers-list">
                <?php foreach ($this->voters as $item): ?>
                    <?php $user = Engine_Api::_()->getItem('user', $item->user_id); ?>
                    <li>
                        <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array()); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
