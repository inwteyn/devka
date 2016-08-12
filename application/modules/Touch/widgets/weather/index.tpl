<?php
/**
 * SocialEngine
 *
 * @category   Application_Widget
 * @package    Weather
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 7562 2010-10-05 22:17:24Z john $
 * @author     John
 */
?>

<ul class="items">

<div class="touch_weather">
  <?php if( !empty($this->location) ): ?>
    <i>Location:</i>
    <?php echo $this->location->city ?>,
    <?php echo $this->location->country ?>
  <?php endif; ?>
  <span class="weather_ch_location">
    <?php echo $this->htmlLink(
      array(
        'route' => 'default',
        'module' => 'core',
        'controller' => 'widget',
        'action' => 'index',
        'content_id' => $this->identity,
        'view' => 'choose',
      ),
      'Change Location',
      array(
        'class' => 'smoothbox'
      )
    ) ?>
  </span>
      <br />
</div>



<?php if( !empty($this->forecast) ): //echo '<pre>'.htmlspecialchars($this->forecast->asXml()).'</pre>'; ?>

  <?php foreach( $this->forecast->txt_forecast->forecastday as $key => $value ): ?>
    <li>
      <div class="item_photo">
        <img src="<?php echo $value->icons->icon_set[0]->icon_url ?>" alt="<?php $value->icon ?>" />
      </div>
      <div class="item_body">
        <div class="item_title">
          <?php echo $value->title ?>
        </div>
        
        <?php echo $value->fcttext ?>
      </div>
    </li>
  <?php endforeach; ?>

<?php endif; ?>

</ul>