<style type="text/css">
    .hecontest-viewer-photo.active.fade:hover #another-like {
        display: inline;
    }
    #another-like {
        display: none;
        text-decoration: none;
        position: absolute;
        left: 42%;
        top: 40%;

        border-radius: 4px;
        padding: 0.6em 1.2em;
        background: #54aeea;
        color: #fff;
        font-size: 20px;
        cursor: pointer;
    }
    #another-like:hover {
        background: #67B5ED;
    }
</style>
<?php if ($this->contest) : ?>
    <?php if (!$this->ajax): ?>
        <script type="text/javascript">
            en4.core.runonce.add(function () {
                window.addEventListener("hashchange", function() {
                    if(hecontestViewer.isActive) {
                        return;
                    }
                    var id = hecontestCore.getIdFromUrl();
                    if (!isNaN(id) && id != 0) {
                        hecontestViewer.show(id, true);
                    }
                }, false);
                window.addEvent('domready', function (e) {
                    hecontestViewer.init('<?php echo $this->contest->getIdentity(); ?>');
                    var id = hecontestCore.getIdFromUrl();
                    if (!isNaN(id) && id != 0) {
                        hecontestViewer.show(id, true);
                    }
                });
                window.addEvent('scroll', function () {
                    if (window.getScrollTop() <= hecontestCore.scrollDirection) {
                        hecontestCore.scrollDirection = window.getScrollTop();
                        return;
                    }
                    hecontestCore.scrollDirection = window.getScrollTop();
                    if (window.getScrollTop() + 5 >= $$('.active-contest-wrapper')[0].offsetHeight / 3) {
                        hecontestCore.loadMore(1);
                    }
                });
            });
            function clickIfHash(id) {
                var hash = hecontestCore.getIdFromUrl();
                if (!isNaN(hash) && hash  != 0 && hash == id) {
                    hecontestViewer.show(id, false);
                }
            }
        </script>
        <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-4fb1c2793e9a0bfe"></script>

        <div id="hecontest-viewer-wrapper">
            <div style="margin: 35px 25px 15px; overflow: hidden;">
                <div id="addthis_toolbox" class="addthis_toolbox addthis_default_style">
                    <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
                    <a class="addthis_button_tweet"></a>

                    <a id="addthis_counter" class="addthis_counter addthis_pill_style"></a>
                </div>
                <a class="hei hei-angle-left hecontest-viewer-slide hecontest-viewer-slide-left" href="javascript://"></a>
                <a class="hei hei-angle-right hecontest-viewer-slide hecontest-viewer-slide-right" href="javascript://"></a>
                <a class="hei hei-times hecontest-viewer-slide hecontest-viewer-slide-close" href="javascript://"></a>

                <div id="hecontest-viewer-content">
                    <table class="hecontest-viewer-content-table" width="100%">
                        <tbody>
                        <tr>
                            <td class="td" id="hecontest-viewer-photos-wrapper">
                            </td>
                            <td width="25"></td>
                            <td id="hecontest-viewer-info-wrapper">
                                <div id="hecontest-viewer-photo-voters">

                                </div>
                                <div id="hecontest-viewer-photo-comment">

                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div id="hecontest-viewer-toolbar">
                    <div>
                        <div id="hecontest-viewer-photos-desc">
                        </div>
                        <div id="hecontest-controls-wrapper">
                            <div id="hecontest-controls">
                            </div>
                        </div>
                        <div style="clear: both;"></div>
                    </div>
                </div>

            </div>
        </div>
    <?php endif; ?>

    <div class="active-contest-wrapper">
        <div class="tip">
        <span>
            <?php $href = $this->htmlLink($this->baseUrl() . "/help/contact", $this->translate("HECONTEST_Contact us")); ?>
            <?php echo $this->translate('HECONTEST_Recent context description', $href); ?>
        </span>
        </div>
        <?php if ($this->participants && $this->participants->getTotalItemCount()): ?>
            <ul>
                <?php foreach ($this->participants as $participant): $t = $participant->getHref(); ?>
                    <?php $user = $participant->getUser(); if(!$user) continue; ?>
                    <li>
                        <a class="hecontest-item hecontest-item-content"
                           href="<?php echo $this->contest->getHref() . '#' . $participant->getIdentity(); ?>"
                           onclick="clickIfHash('<?php echo $participant->getIdentity(); ?>');"
                           style="background-image: url('<?php echo $participant->getPreviewPhotoUrl(); ?>');">
                        </a>

                        <div class="hecontest-items-info">
                            <a href="<?php echo $user->getHref(); ?>"><?php echo $user->getTitle(); ?></a>
                        <span class="hecontest-info-like">
                            <?php if ($participant->allowLike($this->viewer()->getIdentity())) : ?>
                                <i class="hei hei-thumbs-up-alt"
                                   onclick="hecontestCore.vote(this, '<?php echo $participant->getIdentity(); ?>')"></i>
                            <?php elseif (!$this->viewer()->getIdentity()): ?>
                                <?php $return_url = $this->url(array('action' => 'index'), 'hecontest_general');
                                $href = $this->url(array('return_url' => '64-' . base64_encode($return_url)), 'user_login'); ?>
                                <i class="hei hei-thumbs-up-alt"
                                   onclick="document.location.href='<?php echo $href; ?>'"></i>
                            <?php
                            else : ?>
                                <i class="hei hei-thumbs-up-alt"></i>
                            <?php endif; ?>
                            <span>
                                <?php echo $participant->votes; ?>
                            </span>
                        </span>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <div class="tip">
                <span><?php echo $this->translate("HECONTEST_No participants"); ?></span>
            </div>
        <?php endif; ?>
    </div>
<?php else : ?>
    <div class="tip">
        <span><?php echo $this->translate("HECONTEST_No recent contest"); ?></span>
    </div>
<?php endif; ?>
