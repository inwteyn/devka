<?php
/**
 * Created by PhpStorm.
 * User: Медербек
 * Date: 13.07.2015
 * Time: 12:24
 */

class Page_Model_DbTable_Addresses extends Engine_Db_Table
{
    protected $_rowClass = 'Page_Model_Address';

    public function setPageAddresses($addresses, $page_id, $additional_coordinates)
    {
        $this->delete(array('page_id = ?' => $page_id));

        $i = 0;
        foreach($addresses as $address){
            if(!$address[0] && !$address[1] && !$address[2] && !$address[3] && !$additional_coordinates[$i]) {
                return;
            }
            if($additional_coordinates[$i]){
                $coordinate_arr = explode(';', $additional_coordinates[$i]);
                $markersTbl = Engine_Api::_()->getDbTable('markers', 'page');
                $pageMarker = $markersTbl->createRow(array(
                    'page_id' => $page_id,
                    'latitude' => $coordinate_arr[0],
                    'longitude' => $coordinate_arr[1]
                ));
                $pageMarker->save();
            } else {
                $this->addMarkerByAddress($address, $page_id);
            }
            $additional_address = $this->createRow();
            $additional_address->page_id = $page_id;
            $additional_address->country = $address[0];
            $additional_address->state = $address[1];
            $additional_address->city = $address[2];
            $additional_address->street = $address[3];
            $additional_address->save();
            $i++;
        }
    }

    public function getPageAddresses($page_id)
    {
        $addresses = $this->select()
            ->where('page_id = ?', $page_id)
            ->query()
            ->fetchAll();

        return $addresses;
    }

    public function addMarker(Page_Model_Marker $marker, $page_id)
    {
        $marker->page_id = $page_id;
        $marker->save();
    }

    public function addMarkerByAddress($address, $page_id)
    {
        $marker = Engine_Api::_()->getApi('gmap', 'page')->getMarker($address);
        if ($marker) {
            $this->addMarker($marker, $page_id);
        }
    }
}