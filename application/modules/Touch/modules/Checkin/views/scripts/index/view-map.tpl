<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view-map.tpl  01.12.11 16:00 TeaJay $
 * @author     Taalay
 */
?>

<?php if ($this->markers): ?>
	<script type="text/javascript">
    en4.core.runonce.add(function() {
      new CheckinMap( null, <?php echo $this->markers; ?>, 4, <?php echo $this->bounds; ?>, 'map_canvas_view_map');
    });
	</script>
  <div style="display: inline;">
    <div id="map_canvas_view_map" style="width: 100%; height: 300px; margin: 50px 0 0 0; float: none; "></div>
    <div class="checkin_users">
      <div class="list">
        <?php foreach($this->users as $user) : ?>
          <div class="item">
            <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb_icon'), array('title' => $user->getTitle()))?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
<?php endif; ?>