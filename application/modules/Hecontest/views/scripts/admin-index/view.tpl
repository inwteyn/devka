<?php $this->headScript()->prependFile('application/modules/Hecontest/externals/scripts/admin/core.js'); ?>
<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<?php if ($this->error) : ?>
    <div class="tip">
        <span>
            <?php echo $this->translate($this->error); ?>
        </span>
    </div>
<?php else: ?>

    <div id="admin-hecontest-view-header">
        <div class="admin-hecontest-left">
            <span><h2><?php echo $this->contest->getTitle(); ?></h2></span>
        </div>
        <div class="admin-hecontest-left admin-hecontest-view-contest-controls">
            <div class="admin-hecontest-left">
                <?php if($this->contest->is_active) : ?>
                    <a class='smoothbox' href='<?php echo $this->url(array('action' => 'activate', 'activate' => '2', 'hecontest_id' => $this->contest->getIdentity())); ?>'>
                        <?php echo $this->translate('HECONTEST_Deactivate'); ?>
                        <!--<div class="admin-hecontest-edit"></div>-->
                    </a>
                <?php else: ?>
                    <a class='smoothbox' href='<?php echo $this->url(array('action' => 'activate', 'activate' => '1', 'hecontest_id' => $this->contest->getIdentity())); ?>'>
                        <?php echo $this->translate('HECONTEST_Activate'); ?>
                        <!--<div class="admin-hecontest-edit"></div>-->
                    </a>
                <?php endif; ?>
            </div>
            <div class="admin-hecontest-left">
                <a href='<?php echo $this->url(array('action' => 'edit', 'hecontest_id' => $this->contest->getIdentity())); ?>'>
                    <?php echo $this->translate('HECONTEST_Edit'); ?>
                    <!--<div class="admin-hecontest-edit"></div>-->
                </a>
            </div>
            <div class="admin-hecontest-left">
                <a class="smoothbox"
                   href='<?php echo $this->url(array('action' => 'delete', 'hecontest_id' => $this->contest->getIdentity())); ?>'>
                    <?php echo $this->translate('HECONTEST_Delete'); ?>
                    <!--<div class="admin-hecontest-delete"></div>-->
                </a>
            </div>
            <div class="clear"></div>
        </div>
        <div class="clear"></div>
    </div>

    <div>
        <div class="admin-hecontest-view-description">
            <p>
                <?php echo $this->contest->description; ?>
            </p>
        </div>
        <div class="admin-hecontest-view-prize-photo">
            <img src="<?php echo $this->contest->getPhotoUrl(); ?>">

            <div><?php echo $this->contest->prize_name; ?></div>
        </div>
        <div class="clear"></div>
    </div>

    <div style="margin-top: 20px;">
        <h2><?php echo $this->translate("HECONTEST_Contest Participants"); ?></h2>
    </div>

    <div class="admin-hecontest-view-participants">
        <?php if (count($this->paginator)) : ?>

            <?php foreach ($this->paginator as $participant) : ?>
                <?php $member = $participant->getUser(); if(!$member) continue; ?>
                <div class="item active">
                    <div>
                        <div style="float: left;">
                            <a href="javascript://" onclick="showDetails('<?php echo $participant->getIdentity(); ?>');">
                                <img class="participant-img" src="<?php echo $participant->getPreviewPhotoUrl(); ?>" >
                            </a>
                        </div>
                        <div style="float: left;margin-left: 10px;">
                            <div><?php echo $this->htmlLink($member->getHref(), $member->getTitle(), array('target' => '_blank')); ?>
                            </div>

                            <div><?php echo $participant->date_posted; ?></div>
                            <div>Votes - <?php echo $participant->votes; ?></div>
                            <div>Status - <span id="status"><?php echo $participant->status; ?></span></div>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div style="float: right;">
                        <?php if ($participant->status == 'pending') {
                            $approve = 'inline';
                            $decline = 'none';
                        } else {
                            $approve = 'none';
                            $decline = 'inline';
                        }
                        ?>
                        <a id="approve" style="display: <?php echo $approve; ?>;" href='javascript://'
                           onclick="process('<?php echo $participant->getIdentity(); ?>', this, 'approved');">approve</a>

                        <a id="decline" style="display: <?php echo $decline; ?>;" href='javascript://'
                           onclick="process('<?php echo $participant->getIdentity(); ?>', this, 'pending')">decline</a>

                        <a href='javascript://'
                           onclick="process('<?php echo $participant->getIdentity(); ?>', this, 'remove')">remove</a>
                    </div>
                </div>
            <?php endforeach; ?>

            <div onclick="hidePopup();" id="participants-screen" style="display: none;"></div>
            <div onclick="hidePopup();" id="participant-details" style="display: none;">
                <div onclick="" id="participant-details-wrapper">
                    <div id="participant-img">
                        <a style="background-image: url('');"></a>
                    </div>
                    <div id="participant-descr">
                        <p>
                            <?php echo $participant->description; ?>
                        </p>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>

        <?php else : ?>
            <div class="tip">
                <span><?php echo $this->translate("HECONTEST_No participants"); ?></span>
            </div>
        <?php endif; ?>
    </div>

    <div>
        <?php echo $this->paginationControl($this->paginator, null, null, array(
            'pageAsQuery' => true,
            //'params' => $this->formValues,
        )); ?>
    </div>
<?php endif; ?>