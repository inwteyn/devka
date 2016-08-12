<?php
$this->headScript()->appendFile('application/modules/Hecontest/externals/scripts/countdown.js');
$this->headScript()->appendFile('application/modules/Hecontest/externals/scripts/core.js');
$date = strtotime($this->contest->date_end);
$year = date('Y', $date);
$month = date('n', $date) - 1;
$day = date('d', $date);
$hour = date('G', $date);
$minute = date('i', $date);
$s = date('s', $date);

$date = time();
$year2 = date('Y', $date);
$month2 = date('n', $date) - 1;
$day2 = date('d', $date);
$hour2 = date('G', $date);
$minute2 = date('i', $date);
$s2 = date('s', $date);

$widget_uid = uniqid('hecontest-countdown-');
?>
<script type="text/javascript">
    $(window).addEvent('load', function () {

        hecontestCore.targetDate = new Date('<?php echo $year; ?>', '<?php echo $month; ?>', '<?php echo $day; ?>',
            '<?php echo $hour; ?>', '<?php echo $minute; ?>');

        hecontestCore.currentDate = new Date('<?php echo $year2; ?>', '<?php echo $month2; ?>', '<?php echo $day2; ?>',
            '<?php echo $hour2; ?>', '<?php echo $minute2; ?>');

        showCountDown(countdown(hecontestCore.targetDate), '<?php echo $widget_uid; ?>');
        setInterval(function () {
            if (hecontestCore.countdown)
                showCountDown(countdown(hecontestCore.targetDate), '<?php echo $widget_uid; ?>');
        }, 1000);
    });
</script>

<style type="text/css">
    .countdown-ul {
        height: auto;
        text-align: center;
    }

    .countdown-ul .item {
        margin: 0 auto 0 15px;
        display: inline-block;
    }

    .countdown-ul li:first-child {
        margin-left: 0;
    }

    .countdown-ul .item .container {
        width: auto;
    }

    .countdown-ul .item .container .digit {
        font-size: 30px;
        text-align: center;
        font-weight: bold;
    }

    .countdown-ul .item .container .label {
        text-align: center;
    }
</style>


<h1 id="countdown-holder"></h1>
<?php $link = $this->htmlLink($this->contest->getHref(), $this->contest->getTItle(), array()); ?>
<div class="heconest-countdown-descr">
    <span><?php echo $this->translate('HECONTEST_Some descr', $link); ?></span>
</div>
<div id="<?php echo $widget_uid; ?>">
    <ul class="countdown-ul">
        <li class="item">
            <div class="container" id="container-months">
                <div style="font-size: 42px;color:#4F7B96;" class="digit">7</div>
                <div class="label">months</div>
            </div>
        </li>
        <li class="item">
            <div class="container" id="container-days">
                <div style="font-size: 42px;color:#4F7B96;" class="digit">18</div>
                <div class="label">days</div>
            </div>
        </li>
    </ul>
    <ul class="countdown-ul">
        <li class="item">
            <div class="container" id="container-hours">
                <div style="color:red;" class="digit">12</div>
                <div class="label">h</div>
            </div>
        </li>
        <li class="item">
            <div class="container" id="container-minutes">
                <div style="color:red;" class="digit">13</div>
                <div class="label">m</div>
            </div>
        </li>
        <li class="item">
            <div class="container" id="container-seconds">
                <div style="color:red;" class="digit">0</div>
                <div class="label">s</div>
            </div>
        </li>
    </ul>
</div>
<!--<br/>
<br/>
<br/>
<ul class="countdown-ul">
    <li class="item">
        <div class="container">
            <div class="digit">7</div>
            <div class="label">M</div>
        </div>
    </li>
    <li class="item">
        <div class="container">
            <div class="digit">18</div>
            <div class="label">D</div>
        </div>
    </li>
    <li class="item">
        <div class="container">
            <div class="digit">12</div>
            <div class="label">H</div>
        </div>
    </li>
    <li class="item">
        <div class="container">
            <div class="digit">13</div>
            <div class="label">m</div>
        </div>
    </li>
    <li class="item">
        <div class="container">
            <div class="digit">0</div>
            <div class="label">s</div>
        </div>
    </li>
</ul>-->
<!--<table style="margin:0 auto;" id="<?php /*echo $widget_uid; */ ?>">
    <tr>
        <?php /*//if ($this->result->m): */ ?>
        <td id="heconest-countdown-months" class="heconest-countdown-days"><?php /*echo $this->result->m; */ ?></td>
        <td width="15"></td>
        <?php /*//endif;
        //if ($this->result->d):
        */
?>
        <td id="heconest-countdown-days" class="heconest-countdown-days"><?php /*echo $this->result->d; */ ?></td>
        <td width="15"></td>
        <?php /*//endif; */ ?>
        <?php /*//if ($this->result->h): */ ?>
        <td id="heconest-countdown-hours" class="heconest-countdown-days"><?php /*echo $this->result->h; */ ?></td>
        <td width="15"></td>
        <?php /*//endif; */ ?>
        <?php /*//if ($this->result->i): */ ?>
        <td id="heconest-countdown-minutes" class="heconest-countdown-days"><?php /*echo $this->result->i; */ ?></td>
        <td width="15"></td>
        <?php /*//endif; */ ?>
        <?php /*//if ($this->result->s): */ ?>
        <td id="heconest-countdown-seconds" class="heconest-countdown-days"><?php /*echo $this->result->s; */ ?></td>
        <?php /*///endif; */ ?>
    </tr>
    <tr>
        <?php /*//if ($this->result->m): */ ?>
        <td class="heconest-countdown-label">months</td>
        <td width="15"></td>
        <?php /*//endif;
        //if ($this->result->d):
        */
?>
        <td class="heconest-countdown-label">days</td>
        <td width="15"></td>
        <?php /*//endif; */ ?>
        <?php /*//if ($this->result->h): */ ?>
        <td class="heconest-countdown-label">hours</td>
        <td width="15"></td>
        <?php /*//endif; */ ?>
        <?php /*//if ($this->result->i): */ ?>
        <td class="heconest-countdown-label">min</td>
        <td width="15"></td>
        <?php /*//endif; */ ?>
        <?php /*//if ($this->result->s): */ ?>
        <td class="hecontest-countdown-visible" class="heconest-countdown-label">seconds</td>
        <?php /*//endif; */ ?>
    </tr>
</table>-->
<br/>