<div>
    <ul class='similar-tiles'>
        <?php foreach ($this->items as $item):
            $photoUrl = $item->getPhotoUrl('thumb.profile');
            $photoUrl = $photoUrl ? $photoUrl : rtrim($this->baseUrl(), '/') . '/application/modules/Page/externals/images/nophoto_page_thumb_profile.png';
            ?>
            <li id="tile_page_<?php echo $item->getIdentity() ?>">
                <div>
                    <a class="tile-item-photo" href="<?php echo $item->getHref() ?>"
                       style="display: block;background-image: url(<?php echo $photoUrl ?>);"></a>
                    <a class="tile-item-title"
                       href="<?php echo $item->getHref() ?>">
                        <?php echo $this->string()->truncate($item->getTitle(), 13); ?>
                    </a>
                    <div class="similar-tile-like-wrapper">
                        <?php echo $this->likeButton($item); ?>
                    </div>
                </div>
            </li>
        <?php endforeach ?>
    </ul>
</div>

<style>
    .similar-tiles li:hover .similar-tile-like-wrapper {
        opacity: 1;
    }
    .similar-tile-like-wrapper {
        position: absolute;
        margin: 0 auto;
        top: 45%;
        left: 35%;
        opacity: 0;
    }
    .similar-tile-like-wrapper .like_button_link {
        padding: 3px 5px;
    }
    .similar-tile-like-wrapper .like_menu_switcher {
        display: none;
    }
    .similar-tiles {
        width: 100%;
        overflow: hidden;
    }

    .similar-tiles > li {
        display: block;
        float: left;
        margin: 0.5%;
        position: relative;
        width: 49%;
    }

    .similar-tiles li a.tile-item-photo {
        background-position: center center;
        background-size: cover;
        display: block !important;
        padding-top: 100%;
    }

    .similar-tiles li a.tile-item-title {
        opacity: 0;
        font-size: 13px;
        overflow: hidden;
        text-align: center;
        /*transition: opacity .7s ease 0s;
        -webkit-transition: opacity .7s ease 0s;*/
        bottom: 0;
        color: #FFFFFF;
        padding: 7px 0 7px;
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

    .similar-tiles li:hover a.tile-item-title {
        /*transition: opacity 1s ease 0s;*/
        opacity: 1;
    }
</style>