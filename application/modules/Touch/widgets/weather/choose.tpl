<?php
/**
 * SocialEngine
 *
 * @category   Application_Widget
 * @package    Weather
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: choose.tpl 8273 2011-01-19 04:41:46Z char $
 * @author     John
 */
?>
<div class="layout_content">
  <form action="<?php echo $this->url() ?>" method="post" class="touchform">
    <input type="text" name="location" style="width: 100%; margin: 5px 0px;"/>
    <button type="submit">Submit</button>
  </form>
</div>


<?php if( !empty($this->locations) ): ?>

  <ul>
    <?php foreach( $this->locations as $location ): ?>
      <li>
        <?php echo $this->htmlLink($this->url(array('location' => $location->name)), $location->name) ?>
      </li>
    <?php endforeach; ?>
  </ul>

<?php endif; ?>



<?php if( $this->resolved ): ?>

  <script type="text/javascript">
    window.onload = function() {
      parent.window.location.replace( parent.window.location.href );
    }
  </script>

<?php endif; ?>