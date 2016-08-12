<?php
/**
 * SocialEngine
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: GetPriceBlock.php 7244 2011-09-01 01:49:53Z mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Store_View_Helper_GetPriceBlock extends Zend_View_Helper_Abstract
{
    public function getPriceBlock(Store_Model_Product $item)
    {
        $t = $this->view->partial('application/modules/Store/views/scripts/_price_block.tpl', array('item' => $item, 'view' => $this->view));
        return $t;
    }
}