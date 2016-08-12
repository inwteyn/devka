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

class Page_Model_DbTable_FSearch extends Fields_Model_DbTable_Abstract
{
    public function __construct($itemType, $tableType, $config = array()) {
        $itemType = 'page';
        $tableType = 'search';
        parent::__construct($itemType, $tableType, $config);
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