<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _album_photos.tpl 2011-03-16 16:14 ermek $
 * @author     Ermek
 */

?>

<?php
$total_items = count($this->rate_items);
$counter = 1;
?>
<?php if ($total_items == 0) : ?>

    <div class="he_rate_no_content"><?php echo $this->translate('There are no content.'); ?></div>

<?php else : ?>
    <?php foreach ($this->rate_items as $key => $item) : ?>

        <script type="text/javascript">
            en4.core.runonce.add(function(){
                var rateVar = new RatePopular(<?php echo "{$item['user_id']}, 'user', '{$item['uid']}', {$this->can_rate}"; ?>);
                rateVar.rate_url = '<?php echo $this->rate_url; ?>';
                rateVar.langvars = <?php echo $lang_vars; ?>;
            });
        </script>

        <?php if(isset($this->items[$item['object_id']]) && $this->items[$item['object_id']] !== null) : ?>
            <div class="<?php echo ($counter != $total_items) ? 'he_rate_item' : 'he_rate_item_last'; ?>" id="<?php echo $item['uid'];?>">
                <div class="he_rate_thumb"><?php echo $this->itemPhoto( $this->items[$item['object_id']], 'thumb.icon'); ?></div>

                    <div class="he_rate_title_popular">
                        <?php echo $this->htmlLink( $this->items[$item['object_id']]->getHref(), $this->string()->truncate( $this->items[$item['object_id']]->getTitle(), 15, '...')) ?>
                    </div>


                        <div class="he_rate_cont">
                            <div class="rate_stars_cont" style="width: <?php echo 28*$this->maxRate?>px;">
                                <?php for ($i = 0; $i < $this->maxRate; $i++){
                                    if (($i + 0.125) >$item['item_score'] ) {
                                        $star_value = '-o';
                                    } else if (($i + 0.375) > $item['item_score']) {
                                        $star_value = '-half-o';
                                    } else if (($i + 0.625) > $item['item_score']) {
                                        $star_value = '-half-o';
                                    } else if (($i + 0.875) > $item['item_score']) {
                                        $star_value = '-half-o';
                                    } else {
                                        $star_value = '';
                                    }
                                    ?>
                                    <i class="rate_style hei-star<?php echo $star_value;?>" id="rate_star_<?php echo $item['object_id']?>_<?php echo ($i + 1)?>"></i>
                                <?php }?>
                            </div>
                            <div class="item_rate_info">
                                <?php $this->translate('Score:') ?> <span class="item_score"><?php echo $item['item_score']?>/<?php echo $this->maxRate?></span>
                                <span class="item_votes"><?php echo  $item['rate_count']  ?></span>
                                <a class="item_voters" href="javascript://"><?php echo $this->translate(array('vote', 'votes', (($this->rate_info) ? $this->rate_info['rate_count'] : 0))); ?></a>
                            </div>
                            <div class="rate_loading display_none"><span class="rate_loader"><?php $this->translate('Loading ...') ?></span></div>
                        </div>
                        <div class="clr"></div>



<!--                        --><?php //echo $this->ratePopular($this->item_type, $item['object_id'], true, true, $this->period); ?>
                    <?php endif;?>

                </div>
                <div class="clr"></div>

            <?php $counter++ ?>

    <?php endforeach; ?>
    <?php if ($counter === 1) : ?>
        <div class="he_rate_no_content"><?php echo $this->translate('There are no content.'); ?></div>
    <?php endif; ?>
<?php endif; ?>
