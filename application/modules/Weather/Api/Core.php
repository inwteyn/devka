<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Weather
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-12-21 11:45 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Weather
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Weather_Api_Core extends Core_Api_Abstract
{
    public function getLocationData($location)
    {

      $BASE_URL = "http://query.yahooapis.com/v1/public/yql";
      $yql_query = 'select * from weather.forecast where woeid in (select woeid from geo.places(1) where text="'.$location.'")';
      $yql_query_url = $BASE_URL . "?q=" . urlencode($yql_query) . "&format=json";
      $session = curl_init($yql_query_url);
      curl_setopt($session, CURLOPT_RETURNTRANSFER,true);
      $json = curl_exec($session);
      $phpObj =  json_decode($json);





     $settings = Engine_Api::_()->getApi('settings', 'core');
     $us = $settings->getSetting('weather.unit_system', 'us') == 'si' ? 'c' : 'f';

      $us = 'c';

     $cache = Engine_Cache::factory();

     $cacheJsonContent = $phpObj;

     $cacheJsonContent =$cacheJsonContent->query->results->channel;


        if( $cacheJsonContent !== false ) {

          $cache_us = strtolower($cacheJsonContent->units->temperature);

          $converted_forecast = array();

              if($us != $cache_us) {
                if($us == 'f' and $cache_us == 'c') {
                  $converted_forecast['current']['temp'] = round($cacheJsonContent->item->condition->temp*(9/5)+32);
                  $converted_forecast['current']['wind_speed'] = round($cacheJsonContent->wind->speed*2.2369, 1);
                }
              if($us == 'c' and $cache_us == 'f') {
                $converted_forecast['current']['temp'] = round(($cacheJsonContent->item->condition->temp-32)*(5/9));
                $converted_forecast['current']['wind_speed'] = round($cacheJsonContent->wind->speed/2.2369, 1);
              }


              $converted_forecast['current']['code'] = $cacheJsonContent->item->condition->code;
              //$converted_forecast['current']['temp'] = $cacheJsonContent->item->condition->temp;
              $converted_forecast['current']['text'] = $cacheJsonContent->item->condition->text;

              //wind
              $converted_forecast['current']['wind_direction'] = $cacheJsonContent->wind->direction;
              $converted_forecast['current']['wind_speed'] = $cacheJsonContent->wind->speed;

              $converted_forecast['current']['humidity'] = $cacheJsonContent->atmosphere->humidity;

              $converted_forecast['information']['city'] = $cacheJsonContent->location->city;
              $converted_forecast['information']['region'] = $cacheJsonContent->location->region;


              //print_die($phpObj);
            //  print_die($converted_forecast);

              $cast = array();

              $i = 0;
                foreach($cacheJsonContent->item->forecast as $forecast) {

                    if($forecast->high == 0) {
                      $cast['high'] = 1;
                    }elseif($forecast->low == 0) {
                      $cast['low'] = 1;
                    }

                    if($us == 'f' and $cache_us == 'c') {
                        $cast['high'] = round(($forecast->high*(9/5))+32);
                        $cast['low'] = round(($forecast->low*(9/5))+32);
                        $cast['code'] =$forecast->code;
                        $cast['date'] =$forecast->date;
                        $cast['day'] = $forecast->day;
                        $cast['text'] =$forecast->text;
                    }

                    if($us == 'c' and $cache_us == 'f') {
                        $cast['high'] = round(($forecast->high-32)*(5/9));
                        $cast['low'] = round(($forecast->low-32)*(5/9));
                        $cast['code'] =$forecast->code;
                        $cast['date'] =$forecast->date;
                        $cast['day'] = $forecast->day;
                        $cast['text'] =$forecast->text;
                    }

                    $converted_forecast['forecast_list'][] = $cast;

                  $i++;
                  if($i==4){
                    break;
                  }
                }

            }


            return $converted_forecast;

        }elseif( $cacheJsonContent === false or !$cacheJsonContent ) {
            $url = 'http://query.yahooapis.com/v1/public/yql?q=' . rawurlencode('select woeid from geo.places where text="' . $location . '"');
            $a = curl_init($url);

            curl_setopt($a, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($a, CURLOPT_SSL_VERIFYPEER, false);
            $b = curl_exec($a);
            $x = simplexml_load_string($b);
            curl_close($a);

            $woeid = (string)$x->results->place->woeid;
            if(!$woeid or empty($woeid)) {
                $result_info = array(
                    'location' => $location,
                    'information' => ''
                );

                return $result_info;

            }
            $d = 10;
            $yahoo_api_url = 'http://weather.yahooapis.com/forecastrss?w='. $woeid . '&diagnostics=true&u='. $us .'&d='.$d;

            $uriToFullWeather = curl_init($yahoo_api_url);
            curl_setopt($uriToFullWeather, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($uriToFullWeather, CURLOPT_SSL_VERIFYPEER, false);
            if( is_string($uriToFullWeather) ) {
                $result_info = array(
                    'location' => $location,
                    'information' => '',

                );
                return $result_info;
            }

            $xml = new SimpleXMLElement(curl_exec($uriToFullWeather));
            $xml->registerXPathNamespace('yweather','http://xml.weather.yahoo.com/ns/rss/1.0');
            curl_close($uriToFullWeather);
            $weather_code_link = (string)array_shift($xml->xpath('//channel/link'));

            $as = $xml->xpath('//yweather:forecast');
        
            $container = array();
            foreach($as as $inneras){
                $bs = (array)$inneras->attributes();

                foreach($bs as $key_value){
                    array_push($container,$key_value);
                }
            }
            $forecastObject = new stdClass();


            foreach($container[5] as $key => $value){
                $forecastObject->forecast->$key =$value;
            }

            $needle_file = ($us == 'f') ? '_f.xml' : '_c.xml';
            //$weather_code = str_replace('_f.html', '', substr($weather_code_link, strrpos($weather_code_link, '/') + 1));
            $full_weather_xml_file = str_replace(array('_f.html', '_c.html'), $needle_file, substr($weather_code_link, strrpos($weather_code_link, '/') + 1));
            if(!$full_weather_xml_file) {
                $result_info = array(
                    'location' => $location,
                    'information' => ''
                );
                return $result_info;
            }

            $query = rawurlencode('SELECT forecast FROM rss WHERE url="http://xml.weather.yahoo.com/forecastrss/' . $full_weather_xml_file . '"');
            $yql_url = 'http://query.yahooapis.com/v1/public/yql?q=' . $query . '&format=json';
            $curl_handle = curl_init($yql_url);
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
            if( is_string($curl_handle) ) {
                $result_info = array(
                    'location' => $location,
                    'information' => ''
                );
                return $result_info;
            }
            $jsonContent = curl_exec($curl_handle);
            curl_close($curl_handle);
        }

        $jsonResponse = json_decode($jsonContent);
        $wind = (array)array_shift($xml->xpath('//yweather:wind'));
        $wind_attr = $wind['@attributes'];
        $atmosphere = (array)array_shift($xml->xpath('//yweather:atmosphere'));
        $atmosphere_attr = $atmosphere['@attributes'];
        $units = (array)array_shift($xml->xpath('//yweather:units'));
        $units_attr = $units['@attributes'];
        $information = (array)array_shift($xml->xpath('//yweather:location'));
        $information_attr = $information['@attributes'];
        $condition = (array)array_shift($xml->xpath('//yweather:condition'));
        $condition_attr = $condition['@attributes'];

        if( !is_object($jsonResponse->query->results)) {
            $result_info = array(
                'location' => $location,
                'information' => ''
            );

            return $result_info;
        }
        $wind_degree = ( !$wind_attr['direction'] ) ? 0 : $wind_attr['direction'];
        if(!$wind_degree or $wind_degree == 0) $wind_direction = 'VAR';
        if($wind_degree >= 348.75 or $wind_degree <= 11.25) $wind_direction = 'N';
        if($wind_degree > 11.25 && $wind_degree <= 33.75) $wind_direction = 'NNE';
        if($wind_degree > 33.75 && $wind_degree <= 56.25) $wind_direction = 'NE';
        if($wind_degree > 56.25 && $wind_degree <= 78.75) $wind_direction = 'ENE';
        if($wind_degree > 78.75 && $wind_degree <= 101.25) $wind_direction = 'E';
        if($wind_degree > 101.25 && $wind_degree <= 123.75) $wind_direction = 'ESE';
        if($wind_degree > 123.75 && $wind_degree <= 146.25) $wind_direction = 'SE';
        if($wind_degree > 146.25 && $wind_degree <= 168.75) $wind_direction = 'SSE';
        if($wind_degree > 168.75 && $wind_degree <= 191.25) $wind_direction = 'S';
        if($wind_degree > 191.25 && $wind_degree <= 213.75) $wind_direction = 'SSW';
        if($wind_degree > 213.75 && $wind_degree <= 236.25) $wind_direction = 'SW';
        if($wind_degree > 236.25 && $wind_degree <= 258.75) $wind_direction = 'WSW';
        if($wind_degree > 258.75 && $wind_degree <= 281.25) $wind_direction = 'W';
        if($wind_degree > 281.25 && $wind_degree <= 303.75) $wind_direction = 'WNW';
        if($wind_degree > 303.75 && $wind_degree <= 326.25) $wind_direction = 'NW';
        if($wind_degree > 326.25 && $wind_degree < 348.75) $wind_direction = 'NNW';

        if(!$wind_direction) {
            $wind_direction = 'VAR';
        }
        $condition_attr['wind_direction'] = $wind_direction;
        $condition_attr['wind_speed'] = ($us == 'us') ? $wind_attr['speed'] : round($wind_attr['speed']*1000/3600, 1);
        $condition_attr['humidity'] = ( !$atmosphere_attr['humidity'] ) ? 0 : $atmosphere_attr['humidity'];
//        $jsonResponse->query->results->item->append($forecastObject);
        $response = $jsonResponse->query->results->item;
        array_push($response,$forecastObject);
        



        if( $response and is_array($response) ) {
            $forecast_list = array();
            foreach($response as $key_value) {
                $forecast_list[] = (array)$key_value->forecast;
            }
        }

        $result_info = array(
            'location' => $location,
            'units' => $units_attr,
            'information' => $information_attr,
            'current' => $condition_attr,
            'forecast_list' => $forecast_list,
        );


        if (APPLICATION_ENV == 'production') {
            $cache->save($result_info, $cache_id);
            $cache->setLifetime(3600);
        }

        return $result_info;

    }

    public function checkCanEdit($subject)
    {
        if (!$subject) {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer || $viewer->getIdentity() == 0) {
            return false;
        }

        $can_edit = ($subject->getType() == 'page')
            ? $subject->isTeamMember($viewer)
            : ($subject->getOwner()->getIdentity() == $viewer->getIdentity());

        return $can_edit;
    }

    public function getWeatherLocale($locale = false)
    {
        if (!$locale) {
            $locale = Zend_Registry::get('Zend_Translate')->getLocale();
        }

        $british_english = array('en_AU', 'en_BE', 'en_BW', 'en_BZ', 'en_GB', 'en_GU', 'en_HK', 'en_IE', 'en_IN',
            'en_MT', 'en_NA', 'en_NZ', 'en_PH', 'en_PK', 'en_SG', 'en_ZA', 'en_ZW', 'kw', 'kw_GB');

        $friulian = array('fur', 'fur_IT');

        $swiss_german = array('gsw', 'gsw_CH');

        $norwegian_bokma = array('nb', 'nb_NO');

        $portuguese = array('pt', 'pt_PT');

        $brazilian_portuguese = array('pt_BR');

        $chinese = array('zh', 'zh_CN');

        $sar_china = array('zh_HK', 'zh_MO', 'zh_SG');

        $taiwan = array('zh_TW');

        if (in_array($locale, $british_english)) {
            $locale = 'en-GB';
        } elseif (in_array($locale, $friulian)) {
            $locale = 'it';
        } elseif (in_array($locale, $swiss_german)) {
            $locale = 'de';
        } elseif (in_array($locale, $norwegian_bokma)) {
            $locale = 'no';
        } elseif (in_array($locale, $portuguese)) {
            $locale = 'pt-PT';
        } elseif (in_array($locale, $brazilian_portuguese)) {
            $locale = 'pt-BR';
        } elseif (in_array($locale, $chinese)) {
            $locale = 'zh-CN';
        } elseif (in_array($locale, $sar_china)) {
            $locale = 'zh-HK';
        } elseif (in_array($locale, $taiwan)) {
            $locale = 'zh-TW';
        } elseif ($locale) {
            $locale_arr = explode('_', $locale);
            $locale = ($locale_arr[0]) ? $locale_arr[0] : 'en';
        } else {
            $locale = 'en';
        }

        return $locale;
    }
}