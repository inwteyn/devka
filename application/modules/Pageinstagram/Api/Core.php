<?php

class Pageinstagram_Api_Core extends Core_Api_Abstract
{
    protected $_modules;

    public function getContentTable($type)
    {
        return Engine_Api::_()->getItemTable($type);
    }

    public function getTable()
    {
        return Engine_Api::_()->getDbTable('instagrams', 'page');
    }


}