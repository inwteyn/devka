<?php
/**
 * Created by PhpStorm.
 * User: azama_000
 * Date: 08.09.15
 * Time: 11:04
 */

class Apptouch_ApptouchCometchatController extends Apptouch_Controller_Action_Bridge{

    public function indexIndexAction(){

        $this->_helper->layout->setLayout('chat');

    }
}