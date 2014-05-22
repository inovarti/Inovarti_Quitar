<?php

/**
 * Quitar
 *
 * @category    Quitar
 * @package     Inovarti_Quitar
 * @copyright   Copyright (c) 2013 Inovarti. (http://www.inovarti.com.br)
 */
class Inovarti_Quitar_Model_Api extends Mage_Api_Model_Resource_Abstract {

    const LIMIT = 50;

    protected $_resource;
    protected $_connection;

    public function orders($params) {
        $params = json_decode($params, true);

        $limit = $this->_getParamValue($params, 'limit', self::LIMIT);
        $orderId = $this->_getParamValue($params, 'orderId');

        $select = $this->_getConnection()
                ->select()
                ->from(array('o' => $this->_getResource()->getTableName('sales/order')), array(
                    'OrderId' => 'entity_id',
                    'IncrementId' => 'increment_id',
                    'TotalCost' => 'base_grand_total',
                    'Email' => 'customer_email',
                ))
                ->join(array('a' => $this->_getResource()->getTableName('sales/order_address')), 'a.parent_id = o.entity_id', array(
                    'FirstName' => 'firstname',
                    'LastName' => 'lastname',
                    'Phone' => 'telephone',
                    'City' => 'city',
                    'Address' => $this->_getDbExprField('address'),
                    'More' => $this->_getDbExprField('more'),
                    'Number' => $this->_getDbExprField('number'),
                    'ZipCode' => 'postcode',
                    'Reverse' => new Zend_Db_Expr('0')
                ))
                ->joinLeft(array('r' => $this->_getResource()->getTableName('directory/country_region')), 'r.region_id = a.region_id', array(
                    'State' => 'code'
                ))
                ->joinLeft(array('t' => $this->_getResource()->getTableName('sales/shipment_track')), 't.order_id = o.entity_id', array(
                    'TrackingNumber' => 'track_number'
                ))
                ->where('o.status NOT IN(?)', array('canceled', 'holded'))
                ->where('a.address_type = ?', 'shipping')
                ->where('t.carrier_code LIKE ?', '%correios%')
                ->order('o.entity_id ASC')
                ->limit($limit);

        if ($orderId) {
            $select->where('o.entity_id > ?', $orderId);
        }

        return json_encode($this->_getConnection()->fetchAll($select));
    }

    protected function _getDbExprField($field) {
        $line = Mage::getStoreConfig('quitar/fields/' . $field);
        if ($line) {
            $expr = 'SUBSTRING_INDEX(SUBSTRING_INDEX(a.street, "\n", ' . $line . '), "\n", -1)';
        } else {
            $expr = '""';
        }

        return new Zend_Db_Expr($expr);
    }

    protected function _getOrderIdByIncrementId($incrementId) {
        $select = $this->_getConnection()
                ->select()
                ->from(array('o' => $this->_getResource()->getTableName('sales/order')), array('entity_id'))
                ->where('o.increment_id = ?', $incrementId);
        Mage::log("_getOrderIdByIncrementId(): " . print_r($select->__toString(), 1));
        return $this->_getConnection()->fetchOne($select);
    }

    protected function _getParamValue($params, $key, $default = null) {
        if (isset($params[$key]) && $params[$key]) {
            $value = $params[$key];
        } else {
            $value = $default;
        }

        return $value;
    }

    protected function _getResource() {
        if (is_null($this->_resource)) {
            $this->_resource = Mage::getSingleton('core/resource');
        }
        return $this->_resource;
    }

    protected function _getConnection() {
        if (is_null($this->_connection)) {
            $this->_connection = $this->_getResource()->getConnection('core_read');
        }
        return $this->_connection;
    }

}
