<?php

class Store_AdminCurrencyController extends Core_Controller_Action_Admin {
    public function init() {
        $this->view->activeMenu = 'store_admin_main_settings';
    }

    public function indexAction() {
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $this->view->defaultcurrency = $settings->getSetting('payment.currency', 'USD');
        $this->view->multicurrency = $settings->getSetting('hestore.multicurrency.enabled', 0);
        $this->view->currencies = Engine_Api::_()->getDbTable('currencies', 'store')->fetchAll();
    }

    public function setMultiCurrencySettingAction() {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('hestore.multicurrency.enabled', $this->_getParam('enabled'));
    }

    public function changeStatusAction() {
        try {
            Engine_Api::_()->getDbTable('currencies', 'store')->changeCurrencyStatus($this->_getParam('id'), $this->_getParam('status'));
        } catch (Exception $e) {
            $this->view->success = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Unknown database error');
            throw $e;
        }
        $this->view->success = true;
        $this->view->new_status = $this->_getParam('status') ? 0 : 1;
    }

    public function updateCurrencyAction() {
        try {
            Engine_Api::_()->getDbTable('currencies', 'store')->updateCurrency($this->_getParam('id'), $this->_getParam('value'));
        } catch (Exception $e) {
            $this->view->success = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Unknown database error');
            throw $e;
        }
        $this->view->success = true;
    }

    public function setDefaultPaymentCurrencyAction() {
        try {
            Engine_Api::_()->getDbTable('settings', 'core')->setSetting('payment.currency', $this->_getParam('currency'));
            Engine_Api::_()->getDbTable('currencies', 'store')->changeCurrencyStatus($this->_getParam('id'), 0);
            $this->view->success = true;
        } catch (Exception $e) {
            $this->view->success = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Unknown database error');
            throw $e;
        }
    }
}