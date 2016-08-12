<div >
  <ul class='timeline-tiles' id="user_tile_friends">
    <?php foreach ($this->friends as $membership):
      if (!isset($this->friendUsers[$membership->resource_id])) continue;
      $member = $this->friendUsers[$membership->resource_id];
      $photoUrl = $member->getPhotoUrl('thumb.profile');
      $photoUrl = $photoUrl ? $photoUrl : rtrim($this->baseUrl(), '/') . '/application/modules/User/externals/images/nophoto_user_thumb_profile.png';
      ?>
      <li id="tile_friend_<?php echo $member->getIdentity() ?>">
        <div>
          <a class="tile-item-photo" href="<?php echo $member->getHref() ?>"
             style="display: block;background-image: url(<?php echo $photoUrl ?>);"></a>
          <a class="tile-item-title" href="<?php echo $member->getHref() ?>"><?php echo $member->getTitle() ?></a>
        </div>
      </li>
    <?php endforeach ?>
  </ul>
</div>

<style>
  .timeline-tiles {
    width: 100%;
    overflow: hidden;
  }
  .timeline-tiles > li {
    display: block;
    float: left;
    margin: 0.5%;
    position: relative;
    width: 32.333%;
  }

  .timeline-tiles li a.tile-item-photo {
    background-position: center center;
    background-size: cover;
    display: block !important;
    padding-top: 100%;
  }

  .timeline-tiles li a.tile-item-title {
    opacity: 0;
    transition: opacity .7s ease 0s;
    -webkit-transition : opacity .7s ease 0s;
    bottom : 0;
    color: #FFFFFF;
    padding: 14px 9px 9px;
    position: absolute;
    width: 100%;
    text-shadow: 0 1px 3px rgba(64, 64, 64, .7);
    background: -moz-linear-gradient(top, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.19) 33%, rgba(0, 0, 0, 0.5) 100%); /* FF3.6+ */
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, rgba(0, 0, 0, 0)), color-stop(33%, rgba(0, 0, 0, 0.19)), color-stop(100%, rgba(0, 0, 0, 0.5))); /* Chrome,Safari4+ */
    background: -webkit-linear-gradient(top, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.19) 33%, rgba(0, 0, 0, 0.5) 100%); /* Chrome10+,Safari5.1+ */
    background: -o-linear-gradient(top, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.19) 33%, rgba(0, 0, 0, 0.5) 100%); /* Opera 11.10+ */
    background: -ms-linear-gradient(top, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.19) 33%, rgba(0, 0, 0, 0.5) 100%); /* IE10+ */
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.19) 33%, rgba(0, 0, 0, 0.5) 100%); /* W3C */
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#00000000', endColorstr='#80000000', GradientType=0); /* IE6-9 */
  }

  .timeline-tiles li:hover a.tile-item-title {
    transition: opacity 1s ease 0s;
    opacity: 1;
  }
</style>