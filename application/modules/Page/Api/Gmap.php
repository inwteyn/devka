<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Gmap.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Api_Gmap extends Core_Api_Abstract
{
    protected $_ApiKey;

    public function getApiKey()
    {
        if ($this->_ApiKey == null) {
            $this->_ApiKey = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('page.gmapkey');
        }

        return $this->_ApiKey;
    }

    public function validateAddress($address)
    {
        if (is_array($address)) {
            $address = implode(',', $address);
        }

        return $address;
    }

    public function getMarker($address)
    {
        $address = $this->validateAddress($address);
        if ($address == "") {
            return array();
        }
        $address = rawurlencode($address);
        $url = $this->getGMapUrl($address);

        $marker = Engine_Api::_()->getDbTable('markers', 'page')->createRow();
        if ( ($result = file_get_contents($url)) != false ){
            $resultParts = Zend_Json::decode($result);

            if($resultParts['status'] == 'OK'){
                $marker->latitude = $resultParts['results'][0]['geometry']['location']['lat'];
                $marker->longitude = $resultParts['results'][0]['geometry']['location']['lng'];
            } else {
                return false;
            }

        }

        return $marker;
    }

    public function deleteMarker($page)
    {
        if (!($page instanceof Page_Model_Page)){
            throw new Exception('Wrong argument passed.');
        }

        if (!$page->getIdentity()){
            throw new Exception('Wrong page does not exists.');
        }

        $markerTable = Engine_Api::_()->getDbTable('markers', 'page');
        $markerTable->delete("page_id = {$page->getIdentity()}");

        return true;
    }

    public function getMapBounds($markers)
    {
        $minLat = 200;
        $maxLat = -200;
        $minLng = 200;
        $maxLng = -200;

        if (count($markers) == 0) {
            return array();
        } elseif (count($markers) == 1) {
            $marker = reset($markers);
            $minLat = $maxLat = $marker['lat'];
            $minLng = $maxLng = $marker['lng'];
        } else {
            foreach($markers as $marker) {
                if (empty($marker['lng']) || empty($marker['lat'])) continue;

                if ($marker['lng'] <= $minLng) {$minLng = $marker['lng'];}
                if ($marker['lng'] >= $maxLng) {$maxLng = $marker['lng'];}
                if ($marker['lat'] <= $minLat) {$minLat = $marker['lat'];}
                if ($marker['lat'] >= $maxLat) {$maxLat = $marker['lat'];}
            }
        }

        if ($minLat == $maxLat && $minLng == $maxLng) {
            $minLat -= 0.0009;
            $maxLat += 0.0009;
            $minLng -= 0.0009;
            $maxLng += 0.0009;
        }

        $mapCenterLat = (float)($minLat + $maxLat) / 2;
        $mapCenterLng = (float)($minLng + $maxLng) / 2;

        if ( $minLat == 200 || $maxLat == -200 || $minLng == 200 || $maxLng == -200 ) {
            $minLat = '';
            $maxLat = '';
            $minLng = '';
            $maxLng = '';
            $mapCenterLat = '';
            $mapCenterLng = '';
        }

        return array(
            'min_lat' => $minLat,
            'max_lat' => $maxLat,
            'min_lng' => $minLng,
            'max_lng' => $maxLng,
            'map_center_lat' => $mapCenterLat,
            'map_center_lng' => $mapCenterLng
        );
    }

    public function getGMapUrl($address)
    {
        if ($address == ""){
            return "";
        }

        $prefix = (constant('_ENGINE_SSL') ? 'https://' : 'http://');
        return $prefix.'maps.googleapis.com/maps/api/geocode/json?address='.$address.'&sensor=false';
    }

    public function getMapJS()
    {
        $prefix = (constant('_ENGINE_SSL') ? 'https://' : 'http://');

        return "<script src=".$prefix."maps.google.com/maps/api/js?sensor=false type='text/javascript'></script>";
    }

    public function getPageMarker($page)
    {
        $pageMarkers = $page->getMarker();
        if (!$pageMarkers) {
            return array(
                'marker_id' => 0,
                'lat' => 0,
                'lng' => 0,
                'pages_id' => $page->page_id,
                'pages_photo' => $page->getPhotoUrl('thumb.normal'),
                'title' => $page->getTitle(),
                'desc' => Engine_String::substr($page->getDescription(),0,200),
                'url' => $page->getHref()
            );
        }

        $markers = array();

        foreach($pageMarkers as $pageMarker){
            $marker = array(
                'marker_id' => $pageMarker->marker_id,
                'lat' => $pageMarker->latitude,
                'lng' => $pageMarker->longitude,
                'pages_id'=>$page->page_id,
                'pages_photo'=>$page->getPhotoUrl('thumb.normal'),
                'title'=>$page->getTitle(),
                'desc'=>Engine_String::substr($page->getDescription(),0,200),
                'url' => $page->getHref()
            );
            array_push($markers, $marker);
        }


        return $markers;
    }

    public function getMarkers($paginator, $get_coordinates = true)
    {
        $markers = array();
        $view = Zend_Registry::get('Zend_View');
        if ($get_coordinates) {
            $markersTbl = Engine_Api::_()->getDbTable('markers', 'page');
            $page_ids = array();
            foreach( $paginator as $page ) {
                $page_ids[] = $page->page_id;
            }

            $marker_list = $markersTbl->getByPageIds($page_ids);
            foreach ($marker_list as $marker) {
                $page = $paginator->getRowMatching(array('page_id' => $marker->page_id));

                if( !$page ) continue;
                $liked = (Engine_Api::_()->like()->isLike($page))? 'liked_item' : '';
                $identity = $page->getIdentity();
                $liked_block = (Engine_Api::_()->like()->isLike($page))? 'none' : 'block';
                $liked_none = (Engine_Api::_()->like()->isLike($page)) ? 'block' : 'none';
                $guid = $page->getGuid();
                $markers[$page->page_id] = array(
                    'marker_id' => $marker->marker_id,
                    'lat' => $marker->latitude,
                    'lng' => $marker->longitude,
                    'pages_id' => $page->page_id,
                    'pages_photo' => $page->getPhotoUrl('thumb.normal'),
                    'title' => $page->getTitle(),
                    'desc' => Engine_String::substr($page->getDescription(),0,200),
                    'url' => $page->getHref(),
                    'map_rate' => Engine_Api::_()->hasModuleBootstrap('rate') ? $view->itemRate('page', $page->page_id): '',
                    'map_like' =>
                    '<div class="item '.$liked.'" id="page_map_like_'.$page->page_id.'">
                        <div onmouseover="listShowLike('.$identity.')" onmouseout="listHideLike('.$identity.')" style="position:relative">
                            <div class="page_list_browser_likebox list_page_status_'.$identity.'">
                                <span style="display:'.$liked_block.'">
                                    <a href="javascript:void(0)" class="like_button_link list_like" onfocus="this.blur();" id="mapsLike_'.$guid.'">
                                        <i class="hei hei-thumbs-o-up"></i>
                                        <span class="like_button">
                                            Like
                                        </span>
                                    </a>
                                </span>
                                <span style="display:'.$liked_none.'">
                                    <a href="javascript:void(0)" class="like_button_link list_unlike" onfocus="this.blur();" id="mapsUnlike_'.$guid.'">
                                        <i class="hei hei-thumbs-o-down"></i>
                                        <span class="unlike_button">
                                            Unlike
                                        </span>
                                    </a>
                                </span>
                            </div>
                            <div class="page_button_loader hidden list_page_loader_like_'.$identity.'"></div>
                        </div>
                    </div>'
                ,

                );
            }

            return $markers;
        }

        return $markers;
    }
}