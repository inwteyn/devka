<?php
$total_items = count($this->rate_items);
$counter = 1;

$lang_vars = $this->jsonInline(array(
    'title' => $this->translate('Who has voted?'),
    'list_title1' => $this->translate('Everyone'),
    'list_title2' => $this->translate('Friends')
));
?>

<?php if ($total_items == 0) : ?>

    <div class="he_rate_no_content"><?php echo $this->translate('There are no content.'); ?></div>

<?php else : ?>

    <?php foreach ($this->rate_items as $key => $item) : ?>

        <?php if (isset($this->items[$item['object_id']]) && $this->items[$item['object_id']] !== null) : ?>
            <div class="<?php echo ($counter != $total_items) ? 'he_rate_item' : 'he_rate_item_last'; ?>">
                <div
                    class="he_rate_thumb"><?php echo $this->itemPhoto($this->items[$item['object_id']], 'thumb.icon'); ?></div>
                <div class="he_rate_body">
                    <div class="he_rate_title">
                        <?php echo $this->htmlLink($this->items[$item['object_id']]->getHref(), $this->string()->truncate($this->items[$item['object_id']]->getTitle(), 15, '...')) ?>
                    </div>

                    <div class="he_rate_cont" id="rate_uid_<?php echo $item['object_id']; ?>">
                        <div class="rate_stars_cont" style="width: <?php echo 28 * $this->maxRate ?>px;">
                            <?php for ($i = 0; $i < $this->maxRate; $i++) {
                                if (($i + 0.125) > $item['avg_score']) {
                                    $star_value = '-o';
                                } else if (($i + 0.375) > $item['avg_score']) {
                                    $star_value = '-half-o';
                                } else if (($i + 0.625) > $item['avg_score']) {
                                    $star_value = '-half-o';
                                } else if (($i + 0.875) > $item['avg_score']) {
                                    $star_value = '-half-o';
                                } else {
                                    $star_value = '';
                                }
                                ?>
                                <i class="rate_style hei-star<?php echo $star_value; ?>"
                                   id="rate_star_<?php echo($i + 1) ?>"></i>
                            <?php } ?>
                        </div>
                        <div class="item_rate_info">
                            <?php $this->translate('Score:') ?>
                            <span class="item_score">
                                <?php echo $item['total_score'] / $item['rate_count']; ?>
                                /
                                <?php echo $this->maxRate; ?>
                            </span>
                            <span class="item_votes">
                                <?php echo ($item['rate_count']) ? $item['rate_count'] : 0; ?>
                            </span>
                            <a class="item_voters" href="javascript://">
                                <?php echo $this->translate(array('vote', 'votes', (($item['rate_count']) ? $item['rate_count'] : 0))); ?>
                            </a>
                        </div>
                        <div class="rate_loading display_none">
                            <span class="rate_loader">
                                <?php $this->translate('Loading ...'); ?>
                            </span>
                        </div>
                    </div>
                    <div class="clr"></div>

                </div>
                <div class="clr"></div>
            </div>



            <script type="text/javascript">
                en4.core.runonce.add(function () {
                    var rateVar = new Rate(<?php echo "{$item['object_id']}, 'user', '{$item['object_id']}', {$this->can_rate}"; ?>);
                    rateVar.rate_url = '<?php echo $this->rate_url; ?>';
                    rateVar.langvars = <?php echo $lang_vars; ?>;
                });
            </script>

            <?php $counter++ ?>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php if ($counter === 1) : ?>
        <div class="he_rate_no_content"><?php echo $this->translate('There are no content.'); ?></div>
    <?php endif; ?>
<?php endif; ?>