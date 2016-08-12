<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 31.01.11
 * Time: 10:58
 * To change this template use File | Settings | File Templates.
 */
class Updates_View_Helper_GetServerHostUrl extends Zend_View_Helper_Abstract
{
	public function getServerHostUrl()
	{
    /**
     * @var  Updates_Api_Core $core
     */
    $core = Engine_Api::_()->getApi('core', 'updates');

    return $core->getServerHostUrl();
	}
}
