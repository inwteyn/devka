<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-07-02 19:14 ermek $
 * @author     Ermek
 */
?>

<div class="he_rate_cont" id="rate_uid_<?php echo $this->rate_uid; ?>">
    <div class="rate_stars_cont" style="width: <?php echo 28*$this->maxRate?>px;">
        <?php for ($i = 0; $i < $this->maxRate; $i++){
            if (($i + 0.125) > $this->item_score) {
                $star_value = '-o';
            } else if (($i + 0.375) > $this->item_score) {
                $star_value = '-half-o';
            } else if (($i + 0.625) > $this->item_score) {
                $star_value = '-half-o';
            } else if (($i + 0.875) > $this->item_score) {
                $star_value = '-half-o';
            } else {
                $star_value = '';
            }
            ?>
            <i class="rate_style hei-star<?php echo $star_value;?>" id="rate_star_<?php echo ($i + 1)?>"></i>
        <?php }?>
    </div>
    <div class="item_rate_info">
        <?php $this->translate('Score:') ?> <span class="item_score"><?php echo $this->item_score?>/<?php echo $this->maxRate?></span>
        <span class="item_votes"><?php echo ($this->rate_info) ? $this->rate_info['rate_count'] : 0; ?></span>
        <a href="javascript://" class="item_voters"><?php echo $this->translate(array('vote', 'votes', (($this->rate_info) ? $this->rate_info['rate_count'] : 0))); ?></a>
    </div>
    <div class="rate_loading display_none"><span class="rate_loader"><?php $this->translate('Loading ...') ?></span></div>
</div>