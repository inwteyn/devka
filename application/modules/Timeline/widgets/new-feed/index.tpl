<?php
$this->headScript()
    ->appendFile($this->baseUrl() . '/application/modules/Timeline/externals/scripts/listener.js')
?>
<script>

</script>
<div id="newfeedtimeline">
<div class="content_feed_time_line">
    <div class="he-col-md-4 tl_left layout_left_timeline"  >

        <?php $cnt = 0;
        $array_symbols = array(
            '.',
            '-'
        );
        foreach ($this->tabs as $tab): ?>


                <div class="generic_layout_container layout_<?php echo str_ireplace($array_symbols, '_',$tab['name']); ?>">
                    <h3> <?php echo $tab['title']; ?></h3>
                    <?php echo $tab['content']; ?>
                </div>
        <?php endforeach; ?>

    </div>
    <div class="he-col-md-8 generic_layout_container layout_middle_timeline" id="timelinefeedtab">
        <?php

            echo $this->content()->renderWidget('timeline.content');


        ?>
    </div>
</div>
</div>
