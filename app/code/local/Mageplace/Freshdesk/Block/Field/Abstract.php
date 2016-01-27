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
 * Class Mageplace_Freshdesk_Block_Field_Abstract
 *
 * @method Mageplace_Freshdesk_Block_Field_Abstract setField
 * @method Mageplace_Freshdesk_Block_Field_Abstract setRowTemplate
 * @method string|null getRowTemplate
 *
 */
class Mageplace_Freshdesk_Block_Field_Abstract extends Mage_Core_Block_Template
{
    CONST CLASS_NAME_REQUIRED_ENTRY = 'required-entry';

    protected $_fieldRowTemplate;
    protected $_fieldRowAdminTemplate;
    protected $_helper;
    protected $_classNames = array();

    public function _construct()
    {
        parent::_construct();

        $this->_helper = $this->helper('freshdesk');
    }

    public function getFieldRowTemplate()
    {
        if ($this->hasData('field_row_template')) {
            return $this->getData('field_row_template');
        }

        if ($this->isAdminArea() && !empty($this->_fieldRowAdminTemplate)) {
            return $this->_fieldRowAdminTemplate;
        }

        return $this->_fieldRowTemplate;
    }

    /**
     * @return Mageplace_Freshdesk_Helper_Data
     */
    public function _helper()
    {
        return $this->_helper;
    }

    public function getField()
    {
        if ($this->_getData('field') === null) {
            throw new Mageplace_Freshdesk_Exception($this->_helper()->__('Wrong field object'));
        }

        return $this->_getData('field');
    }

    public function getFieldId()
    {
        if ($this->_getData('field_id') === null) {
            $this->setData('field_id', $this->getFieldName() . '_' . $this->getField()->getId());
        }

        return $this->_getData('field_id');
    }

    public function getFieldName()
    {
        if ($this->_getData('field_name') === null) {
            $this->setData('field_name', $this->getField()->getName());
        }

        return $this->_getData('field_name');
    }

    public function getFieldLabel()
    {
        if ($this->_getData('field_label') === null) {
            $this->setData('field_label', $this->getField()->getLabel());
        }

        return $this->_getData('field_label');
    }

    public function getOrderIdFieldName()
    {
        if (null === $this->_getData('order_id_field_name')) {
            /** @var Mageplace_Freshdesk_Model_Field $orderField */
            $orderField = Mage::getModel('freshdesk/field')->getOrderField();
            $this->setData('order_id_field_name', is_object($orderField) ? $orderField->getName() : '');
        }

        return $this->_getData('order_id_field_name');
    }

    public function isOrderField()
    {
        return $this->getOrderIdFieldName() == $this->getFieldName();
    }

    public function getOrderId()
    {
        if ($this->isOrderField() && ($orderId = $this->getRequest()->getParam('order_id'))) {
            return $orderId;
        }

        return '';
    }

    public function isRequired()
    {
        if ($this->_getData('is_required') === null) {
            $this->setData('is_required', $this->getField()->isRequired());
        }

        return $this->_getData('is_required');
    }

    public function isAdminArea()
    {
        if ($this->_getData('is_admin') === null) {
            $this->setData('is_admin', $this->getField()->isAdmin());
        }

        return $this->_getData('is_admin');
    }

    public function addFieldClass($className)
    {
        if (is_array($className)) {
            $this->_classNames = array_merge($this->_classNames, $className);
        } else {
            $this->_classNames[] = strval($className);
        }

        return $this;
    }

    public function getFieldClass()
    {
        $classNames = $this->_classNames;

        if ($this->hasData('field_class')) {
            $classNames[] = strval($this->getData('field_class'));
        }

        return implode(' ', array_reverse($classNames));
    }

    protected function _beforeToHtml()
    {
        if ($this->isRequired()) {
            $this->addFieldClass(self::CLASS_NAME_REQUIRED_ENTRY);
        }

        return parent::_beforeToHtml();
    }
}