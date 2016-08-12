<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<?php $map_center = $this->bounds['map_center_lat'] . ',' . $this->bounds['map_center_lng']; ?>

<div class="page_title"><?php echo $this->page_title ?></div>

<div id="map_canvas" class="page_map">
    <img id="page-details-smap"
         src="http://maps.googleapis.com/maps/api/staticmap?center=<?php echo $map_center ?>&size=500x250
            <?php foreach($this->markers as $marker){ ?>
            &markers=color:red|<?php echo $marker['lat'].','.$marker['lng']; ?>&sensor=false
            <?php }?>"
         style="width: 100%">
</div>

<a class="smoothbox view-larger-map-btn" href="<?php echo $this->url(array('page_id' => $this->subject->getIdentity()), 'page_map'); ?>">
    <?php echo $this->translate("View Larger Map"); ?>
</a>