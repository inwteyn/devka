<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Lists.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Model_DbTable_Search extends Engine_Db_Table
{
    /*public function __construct($itemType, $tableType, $config = array()) {
        $itemType = 'page';
        $tableType = 'search';
        parent::__construct($itemType, $tableType, $config);
    }*/
    
	public function getSelect($params = array())
	{
		$select = $this->select();

		if (!empty($params['keyword'])){

      $where = "
        ( title LIKE '%".$params['keyword']."%' AND object <> 'pagediscussion_pagepost' )
          OR
        ( body LIKE '%".$params['keyword']."%' AND object <> 'pagediscussion_pagetopic' )
      ";
			$select->where($where);
		}

		if (!empty($params['page_id'])){
			$select
				->where("page_id = ?", $params['page_id']);
		}

		if (!empty($params['object'])) {
			if (is_array($params['object'])) {
				$where = "'".implode("','", $params['object'])."'";
				$select
					->where("object IN (".$where.")");
			}else{
				$select
					->where("object = ?", $params['object']);
			}
		}

		if (!empty($params['object_id'])) {
			$select
				->where("object_id = ?", $params['object_id']);
		}

		if (!empty($params['title'])) {
			$select
				->where("title LIKE '%".$params['title']."%' AND object <> 'pagediscussion_pagepost'");
		}

		if (!empty($params['body'])) {
			$select
				->where("body LIKE '%".$params['body']."%' AND object <> 'pagediscussion_pagetopic'");
		}

		return $select;
	}
	
	public function getItems($params = array(), $categorized = true, $itemFetched = false)
	{
		$select = $this->getSelect($params);
		$rawData = $this->fetchAll($select);
		$storage = Engine_Api::_()->storage();
		$api = Engine_Api::_()->getApi('core', 'page');
		$items = array();
		foreach ($rawData as $data){

			if ($categorized){
				$type = $api->shortenType($data['object']);


				if ($data['object'] == 'store_product') {

						$data['photo_id'] = Engine_Api::_()->getItem($data['object'], (int)$data['object_id'])->getPhotoUrl();

				}elseif ($data['photo_id']) {
					$photo = $storage->get($data['photo_id']);

					if ($photo){
						$data['photo_id'] = $photo->map();
					}else{
						$data['photo_id'] = $api->getNoPhoto($data['object']);
					}
				}else{
					$data['photo_id'] = $api->getNoPhoto($data['object']);
				}

				if ($itemFetched){
					$items[$type][] = Engine_Api::_()->getItem($data['object'], (int)$data['object_id']);
				}else{
					$items[$type][] = $data;
				}
			}else{
				if ($itemFetched){
					$items[] = Engine_Api::_()->getItem($data['object'], (int)$data['object_id']);
				}else{
					$items[] = $data;
				}
			}
		}

		$data = array();
		if ($categorized){
			foreach ($items as $key => $value) {
				$data[$key] = Zend_Paginator::factory($value);
        $data[$key]->setItemCountPerPage(100);
			}

			return $data;
		}

		return Zend_Paginator::factory($items);
	}
	
	public function saveData($data)
	{
		if ($data instanceof Core_Model_Item_Abstract) {
			$params = array(
				'object' => $data->getType(),
				'object_id' => (int)$data->getIdentity(),
				'page_id' => (int)$data->getPage()->getIdentity(),
				'title' => strip_tags($data->getTitle()),
				'photo_id' => (int)(isset($data->photo_id) ? $data->photo_id : ($data->getType() == 'pagealbumphoto' ? $data->file_id : 0)),
				'body' => strip_tags(isset($data->description) ? $data->description : (isset($data->body) ? $data->body : ''))
			);
		} elseif (is_array($data)) {
			$params = $data;
		} else {
			return false;
		}

		return $this->saveDataFromArray($params);
	}

	public function saveDataFromArray(array $params)
	{
		$title = (string)$params['title'];
		$body = (string)$params['body'];
		$page_id = (int)$params['page_id'];

		unset($params['title']);
		unset($params['body']);

		$select = $this->getSelect($params);
		$row = $this->fetchRow($select);

		if (!$row){
			$row = $this->createRow();
		}

		$row->object = $params['object'];
		$row->object_id = (int)$params['object_id'];
		$row->photo_id = (int)$params['photo_id'];
		$row->title = $title;
		$row->body = $body;
		$row->page_id = $page_id;

		$row->save();

		return $row;
	}

	public function deleteData($data)
	{
		if ($data instanceof Core_Model_Item_Abstract){
			$params = array(
				'object = ?' => $data->getType(),
				'object_id = ?' => (int)$data->getIdentity(),
				'page_id = ?' => (int)$data->getPage()->getIdentity()
			);
		}elseif (is_array($data)){
			$params = array(
				'object = ?' => $data['object'],
				'object_id = ?' => (int)$data['object_id'],
				'page_id = ?' => (int)$data['page_id']
			);
		}else{
			return false;
		}

		$this->delete($params);

		return $this;
	}


    public function getSearchQuery($params)
    {
        $colsMeta = $this->info('metadata');
        $metaData = Engine_Api::_()->fields()->getFieldsMeta('page');

        $parts = array();
        foreach( $params as $key => $value ) {
            if( !isset($colsMeta[$key]) ) continue;
            $colMeta = $colsMeta[$key];

            // Ignore empty values
            if( (is_scalar($value) && $value === '') ||
                (is_array($value) && empty($value)) ||
                (is_array($value) && array_key_exists('min', $value) && array_filter($value) === array() ) ) {
                continue;
            }

            // Hack for age->birthdate
            if( $key == 'birthdate' || $key == 'birthday' ) {
                if( is_array($value) &&  $value['min'] != $value['max'] ) {
                    $min = null;
                    $max = null;

                    if( !empty($value['min']) ) {
                        $max = date('Y-m-d', (time() - (365 * 24 * 60 * 60) * $value['min']));
                    }
                    unset($value['min']);

                    if( !empty($value['max']) ) {
                        $min = date('Y-m-d', (time() - (365 * 24 * 60 * 60) * $value['max'])
                            - (365 * 24 * 60 * 60)); // Hack for max-age year);
                    }
                    unset($value['max']);

                    if( $min ) {
                        $value['min'] = $min;
                    }
                    if( $max ) {
                        $value['max'] = $max;
                    }
                } else if( is_scalar($value) || $value['min'] == $value['max'] ) {
                    if (!is_scalar($value)) $value = $value['min'];
                    $value = array(
                        'min' => date('Y-m-d', (time() - (365 * 24 * 60 * 60) * ($value + 1) - 1)),
                        'max' => date('Y-m-d', (time() - (365 * 24 * 60 * 60) * $value )),
                    );
                }
            }

            // Set
            if( strtoupper(substr($colMeta['DATA_TYPE'], 0, 3)) === 'SET' ) {
                preg_match('/\((.+)\)/', $colMeta['DATA_TYPE'], $m);
                if( empty($m[1]) ) continue;
                $allowed = $m[1];
                $allowed = explode(',', $m[1]);
                foreach( $allowed as &$al ) {
                    $al = trim($al, '\'",');
                }
                $value = (array) $value;
                $value = array_intersect($allowed, $value);
                if( empty($value) ) continue;
                $value = '%' . join('%', $value) . '%';

                $parts[$key . ' LIKE ?'] = $value;
            }

            // Range
            else if( is_array($value) && (array_key_exists('min', $value) || array_key_exists('max', $value)) ) {
                if( isset($value['min']) && $value['min'] !== '' ) {
                    $parts[$key . ' >= ?'] = $value['min'];
                }
                if( isset($value['max']) && $value['max'] !== '' ) {
                    $parts[$key . ' <= ?'] = $value['max'];
                }
            }

            // Substring?
            // @todo don't really like this
            else if( is_string($value) && ($value[0] == '%' || $value[strlen($value)-1] == '%') ) {
                $parts[$key . ' LIKE ?'] = $value;
            }
            else if ( is_string($value) ){
                $parts[$key . ' LIKE ?'] =  '%' . $value . '%';
            }
            // Scalar
            else if( is_scalar($value) ) {
                $parts[$key . ' LIKE ?'] = $value;
            }
        }

        return $parts;
    }
	
}