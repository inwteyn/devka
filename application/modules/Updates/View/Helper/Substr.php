<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 31.01.11
 * Time: 10:58
 * To change this template use File | Settings | File Templates.
 */
class Updates_View_Helper_Substr extends Zend_View_Helper_Abstract
{
	public function substr($str, $count = 10)
	{
		$count_tmp = (int) ($count - 1);
		return Engine_String::substr($str, 0, $count) . ((Engine_String::strlen($str) > $count_tmp)? '...':'');
	}
}
