<?php
class Store_Model_DbTable_Currencies extends Engine_Db_Table
{
    protected $_rowClass = "Store_Model_Currency";

    public function getActiveCurrencies() {
        $select_currencies = $this->select()->where('enabled = ?', 1);
        return $this->fetchAll($select_currencies);
    }

    public function getCurrencyByCode($code) {
        $select_currencies = $this->select()->where('currency = ?', $code);
        return $this->fetchRow($select_currencies);
    }

    public function changeCurrencyStatus($id, $current_status) {
        $this->update(array('enabled' => $current_status ? 0 : 1), array('id = ?' => $id));
    }

    public function updateCurrency($id, $currency_val) {
        $this->update(array('value' => $currency_val), array('id = ?' => $id));
    }
}