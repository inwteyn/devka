<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Weather
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _weather.tpl 2010-12-21 17:53 ermek $
 * @author     Ermek
 */

$staticBaseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
$this->headLink()->prependStylesheet($staticBaseUrl . '/application/modules/Weather/externals/styles/weather-icons.css');
?>

<?php
    function setWeatherIcon($condid) {

        switch($condid) {
            case 0: $icon  = '<i class="wi wi-tornado icon"></i>';
                break;
            case 1: $icon  = '<i class="wi wi-storm-showers icon "></i>';
                break;
            case 2: $icon  = '<i class="wi wi-tornado icon"></i>';
                break;
            case 3: $icon  = '<i class="wi wi-thunderstorm icon"></i>';
                break;
            case 4: $icon  = '<i class="wi wi-thunderstorm icon"></i>';
                break;
            case 5: $icon  = '<i class="wi wi-snow icon"></i>';
                break;
            case 6: $icon  = '<i class="wi wi-rain-mix icon"></i>';
                break;
            case 7: $icon  = '<i class="wi wi-rain-mix icon"></i>';
                break;
            case 8: $icon  = '<i class="wi wi-sprinkle icon"></i>';
                break;
            case 9: $icon  = '<i class="wi wi-sprinkle icon"></i>';
                break;
            case 10: $icon  = '<i class="wi wi-hail icon"></i>';
                break;
            case 11: $icon  = '<i class="wi wi-showers icon"></i>';
                break;
            case 12: $icon  = '<i class="wi wi-showers icon"></i>';
                break;
            case 13: $icon  = '<i class="wi wi-snow icon"></i>';
                break;
            case 14: $icon  = '<i class="wi wi-storm-showers icon"></i>';
                break;
            case 15: $icon  = '<i class="wi wi-snow icon"></i>';
                break;
            case 16: $icon  = '<i class="wi wi-snow icon"></i>';
                break;
            case 17: $icon  = '<i class="wi wi-hail icon"></i>';
                break;
            case 18: $icon  = '<i class="wi wi-hail icon"></i>';
                break;
            case 19: $icon  = '<i class="wi wi-cloudy-gusts icon"></i>';
                break;
            case 20: $icon  = '<i class="wi wi-fog icon"></i>';
                break;
            case 21: $icon  = '<i class="wi wi-fog icon"></i>';
                break;
            case 22: $icon  = '<i class="wi wi-fog icon"></i>';
                break;
            case 23: $icon  = '<i class="wi wi-cloudy-gusts icon"></i>';
                break;
            case 24: $icon  = '<i class="wi wi-cloudy-windy icon"></i>';
                break;
            case 25: $icon  = '<i class="wi wi-thermometer icon"></i>';
                break;
            case 26: $icon  = '<i class="wi wi-cloudy icon"></i>';
                break;
            case 27: $icon  = '<i class="wi wi-night-cloudy icon"></i>';
                break;
            case 28: $icon  = '<i class="wi wi-day-cloudy icon"></i>';
                break;
            case 29: $icon  = '<i class="wi wi-night-cloudy icon"></i>';
                break;
            case 30: $icon  = '<i class="wi wi-day-cloudy icon"></i>';
                break;
            case 31: $icon  = '<i class="wi wi-night-clear icon"></i>';
                break;
            case 32: $icon  = '<i class="wi wi-day-sunny icon"></i>';
                break;
            case 33: $icon  = '<i class="wi wi-night-clear icon"></i>';
                break;
            case 34: $icon  = '<i class="wi wi-day-sunny-overcast icon"></i>';
                break;
            case 35: $icon  = '<i class="wi wi-hail icon"></i>';
                break;
            case 36: $icon  = '<i class="wi wi-day-sunny icon"></i>';
                break;
            case 37: $icon  = '<i class="wi wi-thunderstorm icon"></i>';
                break;
            case 38: $icon  = '<i class="wi wi-thunderstorm icon"></i>';
                break;
            case 39: $icon  = '<i class="wi wi-thunderstorm icon"></i>';
                break;
            case 40: $icon  = '<i class="wi wi-storm-showers icon""></i>';
                break;
            case 41: $icon  = '<i class="wi wi-snow icon"></i>';
                break;
            case 42: $icon  = '<i class="wi wi-snow icon"></i>';
                break;
            case 43: $icon  = '<i class="wi wi-snow icon"></i>';
                break;
            case 44: $icon  = '<i class="wi wi-cloudy icon"></i>';
                break;
            case 45: $icon  = '<i class="wi wi-lightning icon"></i>';
                break;
            case 46: $icon  = '<i class="wi wi-snow icon"></i>';
                break;
            case 47: $icon  = '<i class="wi wi-thunderstorm icon"></i>';
                break;
            case 3200: $icon  =  '<i class="wi wi-cloud icon"></i>';
                break;
            default: $icon  =  '<i class="wi wi-cloud icon"></i>';
                break;
        }

        return $icon;

    }
?>

<?php
//print_die($this->weather);

if (empty($this->weather['information'])) : ?>
    <div class="weather_city">
        <?php if ($this->can_edit_location) : ?>
            <a href="javascript://" onclick="show_edit_location_box(this);" class="edit_location_data_btn" title="<?php echo $this->translate('weather_edit location'); ?>"></a>
        <?php endif; ?>
        <?php echo ($this->weather['location']) ?  $this->weather['location'] : $this->translate('weather_No location'); ?>
    </div>
    <div class="weather_forecast_body"><?php echo $this->translate('weather_No data found'); ?></div>

<?php else : ?>
    <div>
        <p style="font-size: 18px; padding-bottom:0px; ">
            <?php echo  $this->weather['information']['city']?>
            <?php if($this->weather['information']['region']) : ?>
                <?php echo ",".$this->weather['information']['region'];?>

            <?php endif;?>

        </p>
        <p style="font-size: 14px;">
            <?php $a=str_split($this->weather['current']['date'], 11);
            echo $a[0];?>
        </p>
        <hr style="margin: 4px 2px 2px 2px; "/>
    </div>


    <div class="weather_today_weather">
        <div class="weather_forecast_today_body">
            <?php echo setWeatherIcon($this->weather['current']['code']);?>
           
            <div  style="margin-top: 2%;margin-left: 4px; display: inline-block;">
                <?php
                $weather = 0;
                $unit_system = ($this->unit_system == 'us') ? $this->translate('weather_F') : $this->translate('weather_C');
                $weather = $this->weather['current']['temp'];
                ?>
                <p style="font-size: 24px; color: #a9302a"><?php echo $weather; ?>&deg; <?php echo $unit_system; ?></p>
                <p style="font-size: 16px; color: #a9302a">
                    <?php echo $this->translate(strtolower($this->weather['current']['text'])) ?>

                </p>
            </div>
            <!--        end of temperature-->

            <!-- wind & humidity-->
            <div class="clr"></div>
            <div class="condition ">
                <p style="font-size: 14px;text-align: left">
                    <?php
                    $wind_direction = $this->translate($this->weather['current']['wind_direction']);
                    $wind_speed = $this->weather['current']['wind_speed'];
                    if( !$wind_speed or $wind_speed < 0 ) {
                        $wind_speed = 0;
                    }
                    $unit_speed = ($this->unit_system == 'us') ? 'mph' : 'm/s';
                    $wind_condition = $this->translate('Wind: %1$s  %2$s ' . $unit_speed, $wind_direction, $wind_speed);
                    $humidity = $this->translate('Humidity: ') . $this->weather['current']['humidity'] . '%';
                    echo ($wind_condition) ? $humidity : '';
                    ?>
                </p>
                <p style="font-size: 14px;text-align: left"><?php echo $wind_condition ?></p>
                <div class="clr"></div>
            </div>
            <div class="clr"></div>
        </div>
        <hr style="margin: 4px 2px 2px 2px; "/>
    </div>
    <!--    end of wind & humidity-->


    <!-- forecast for days -->
    <div class="row row-centered">
        <?php foreach ($this->weather['forecast_list'] as $forecast) : ?>
            <?php
            $forecast_condition = str_replace(array('AM ', 'PM '), '', $forecast['text']);

            if (strpos($forecast_condition, '/')) {
                $i = 0;
                $conditions = explode('/', $forecast_condition);
                foreach ($conditions as $condition) {
                    $forecast_condition = ($i > 0) ? $forecast_condition . '/' . $this->translate(strtolower($condition)) : '' . $this->translate(strtolower($condition));
                    $i++;
                }
            } else {
                $forecast_condition = $this->translate($forecast_condition == 'Clear' ? 'WEATHER_' . strtolower($forecast_condition) : strtolower($forecast_condition));
            }
            ?>
            <!--            cells of days-->
            <div class="weather_forecast_body col-centered">
                <!--                day of weather-->
                <div style="clear: both; text-align: center;">
                    <?php echo $forecast['day'] ?>
                </div>
                <!--                end of day of weather-->
                <!--                image of weather-->
                <div class="weather_icon">
                        <?php echo setWeatherIcon($forecast['code']);?>
                </div>

                <!--                end of image of weather-->

                <!-- day & night temperatures-->

                  <?php
                  $high = $forecast['high'];
                  $low = $forecast['low'];
                  ?>
                <p style="display: block;text-align: center">
                    <span style="display:inline-block;font-size: 14px;color: orange;">
                        <?php echo $low; ?>&deg;
                    </span>

                    <span style="display:inline-block;font-size: 14px">
                        <?php echo $high; ?>&deg;
                    </span>
                </p>

                <!-- end of day & night temperatures-->

                <div class="clr"></div>

                <div class="clr"></div>
            </div>
            <!--            end of cells of days-->
        <?php endforeach ?>
    </div>

    <!-- end of forecast of days-->
    <div class="weather_city">
        <?php if ($this->can_edit_location) : ?>
            <a href="javascript://" onclick="show_edit_location_box(this);" class="edit_location_data_btn" title="<?php echo $this->translate('weather_edit location'); ?>"></a>
        <?php endif; ?>
        <?php echo $this->weather['information']['city']; ?>
    </div>

<?php endif; ?>
