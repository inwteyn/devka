<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 19/10/2015
 * Time: 16:42
 * Author:Almazbeck
 */


class Store_Widget_ProductMusicsController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        if( !Engine_Api::_()->core()->hasSubject('store_product') ) return $this->setNoRender();

        /**
         * @var $product Store_Model_Product
         * @var $audiosTbl Store_Model_DbTable_Audios
         */


        $path = Zend_Controller_Front::getInstance()->getControllerDirectory('store_product');
        $path = dirname($path) . '/views/scripts';
        $this->view->addScriptPath($path);

        $product = Engine_Api::_()->core()->getSubject('store_product');
        $this->view->storage = Engine_Api::_()->storage();
        $audiosTbl = Engine_Api::_()->getDbTable('audios', 'store');
        $this->view->audios = $audios = $audiosTbl->getAudios($product->getIdentity());

        if (count($audios) <= 0) {
            return $this->setNoRender();
        }
    }
    protected $_childCount;

   
}
