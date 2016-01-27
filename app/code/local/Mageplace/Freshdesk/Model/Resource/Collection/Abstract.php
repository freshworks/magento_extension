<?php
/**
 * Mageplace Freshdesk extension
 *
 * @category    Mageplace_Freshdesk
 * @package     Mageplace_Freshdesk
 * @copyright   Copyright (c) 2014 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

/**
 * Class Mageplace_Freshdesk_Model_Resource_Collection_Abstract
 */
abstract class Mageplace_Freshdesk_Model_Resource_Collection_Abstract extends Varien_Data_Collection
{
    protected $_freshdeskData;
    protected $_rawData = array();
    protected $_isOrdersRendered = false;
    protected $_sortField;
    protected $_sortDir;

    abstract protected function loadFreshdeskData();

    public function loadData($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        $this->_rawData = $this->loadFreshdeskData();
        $this->_renderFilters()
            ->_renderOrders()
            ->_renderLimit();

        foreach ($this->_rawData as $data) {
            $this->createItem($data);
        }

        $this->_setIsLoaded();

        return $this;
    }

    public function createItem($data)
    {
        $item = $this->getNewEmptyItem();
        $item->addData($data);
        $this->addItem($item);
    }

    public function getSize()
    {
        $this->loadFreshdeskData();
        if (is_null($this->_totalRecords)) {
            foreach ($this->_freshdeskData as $id => $data) {
                if ($this->_filter($data)) {
                    ++$this->_totalRecords;
                }
            }
        }

        return intval($this->_totalRecords);
    }

    public function addFilter($field, $value, $type = 'and')
    {
        $this->addFieldToFilter($field, $value);
        $this->_isFiltersRendered = false;

        return $this;
    }

    public function getFilter($field)
    {
        if (is_array($field)) {
            if (empty($field)) {
                return $this->_filters;
            }

            $result = array();
            foreach ($field as $f) {
                if (array_key_exists($f, $this->_filters)) {
                    $result[] = $this->_filters[$f];
                }
            }

            return $result;
        }

        if (array_key_exists($field, $this->_filters)) {
            return $this->_filters[$field];
        }

        return null;
    }

    public function addFieldToFilter($fields, $conditions = null)
    {
        if (!is_array($this->_filters)) {
            $this->_filters = array();
        }

        if (is_array($fields)) {
            foreach ($fields as $field) {
                if (is_array($conditions)) {
                    foreach ($conditions as $name => $condition) {
                        $this->_filters[$field][$name][] = $condition;
                    }
                } else {
                    $this->_filters[$field]['eq'][] = strval($conditions);
                }
            }
        } else {
            if (is_array($conditions)) {
                foreach ($conditions as $name => $condition) {
                    $this->_filters[strval($fields)][$name][] = $condition;
                }
            } else {
                $this->_filters[strval($fields)]['eq'][] = strval($conditions);
            }
        }

        return $this;
    }

    protected function _renderFilters()
    {
        if ($this->_isFiltersRendered) {
            return $this;
        }

        foreach ($this->_rawData as $k => $data) {
            if (!$this->_filter($data)) {
                unset($this->_rawData[$k]);
            }
        }

        $this->_isFiltersRendered = true;

        return $this;
    }

    protected function _filter(array $data)
    {
        foreach ($this->_filters as $column => $filters) {
            if (!array_key_exists($column, $data)) {
                return false;
            }

            foreach ($filters as $type => $values) {
                if ($type == 'or') {
                    if (!in_array($data[$column], $values)) {
                        return false;
                    }
                } else {
                    foreach ($values as $value) {
                        if ($data[$column] != $value) {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }

    protected function _renderOrders()
    {
        if (!$this->_isOrdersRendered) {
            reset($this->_orders);

            while (current($this->_orders)) {
                uasort($this->_rawData, array($this, 'sortByOrder'));
                next($this->_orders);
            }

            reset($this->_orders);

            $this->_isOrdersRendered = true;
        }

        return $this;
    }

    protected function sortByOrder($a, $b)
    {
        $direction = current($this->_orders);
        $field     = key($this->_orders);
        if (strpos($field, '/') > 0) {
            list($col1, $col2) = explode('/', $field);
            $aField = $a[$col1][$col2];
            $bField = $b[$col1][$col2];
        } else {
            $aField = $a[$field];
            $bField = $b[$field];
        }
        if (is_int($aField)) {
            if ($aField == $bField) {
                return 0;
            } elseif (($direction == 'DESC' && $aField > $bField) || ($direction == 'ASC' && $aField < $bField)) {
                return -1;
            } else {
                return 1;
            }
        } else {
            if ($strnatcmp = strnatcmp(strval($aField), strval($bField))) {
                return ($direction == 'DESC' ? -1 : 1) * $strnatcmp;
            }

            return 0;
        }
    }

    protected function _renderLimit()
    {
        if ($this->_pageSize) {
            $this->_rawData = array_slice($this->_rawData, ($this->getCurPage() - 1) * $this->_pageSize, $this->_pageSize, true);
        }

        return $this;
    }
}