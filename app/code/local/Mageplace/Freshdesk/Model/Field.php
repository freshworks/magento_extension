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
 * Class Mageplace_Freshdesk_Model_Field
 *
 * @method string getLabelInPortal
 * @method string getFieldType
 * @method string getName
 * @method array getChoices
 * @method bool getRequired
 * @method bool getRequiredInPortal
 * @method bool getVisibleInPortal
 * @method bool getEditableInPortal
 * @method array getNestedChoices
 * @method array getNestedTicketFields
 * @method Mageplace_Freshdesk_Model_Field setLayout
 */
class Mageplace_Freshdesk_Model_Field extends Mageplace_Freshdesk_Model_Abstract
{
    const FIELD_DISPLAY_ID     = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_DISPLAY_ID;
    const FIELD_SUBJECT        = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_SUBJECT;
    const FIELD_CREATED_AT     = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_CREATED_AT;
    const FIELD_DESCRIPTION    = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_DESCRIPTION;
    const FIELD_REQUESTER      = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_REQUESTER;
    const FIELD_REQUESTER_ID   = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_REQUESTER_ID;
    const FIELD_REQUESTER_NAME = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_REQUESTER_NAME;
    const FIELD_EMAIL          = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_EMAIL;
    const FIELD_PRIORITY       = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_PRIORITY;
    const FIELD_PRIORITY_NAME  = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_PRIORITY_NAME;
    const FIELD_STATUS         = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_STATUS;
    const FIELD_STATUS_NAME    = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_STATUS_NAME;
    const FIELD_AGENT          = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_AGENT;
    const FIELD_RESPONDER_ID   = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_RESPONDER_ID;
    const FIELD_GROUP          = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_GROUP;
    const FIELD_GROUP_ID       = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_GROUP_ID;
    const FIELD_CUSTOM_FIELD   = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_CUSTOM_FIELD;

    const NAME       = Mageplace_Freshdesk_Model_Freshdesk_Fields::PARAM_NAME;
    const FIELD_TYPE = Mageplace_Freshdesk_Model_Freshdesk_Fields::PARAM_FIELD_TYPE;

    const REGISTER_ORDER_FIELD = 'freshdesk_order_field';

    protected $_isAdmin;

    protected function _construct()
    {
        parent::_construct();

        $this->_init('freshdesk/field');
    }

    public function load($id, $field = null)
    {
        $model  = null;
        $fields = $this->getCollection();
        if ($field === null) {
            $fields = $fields->getItems();
            $model  = $fields[$id];
        } else {
            foreach ($fields as $objField) {
                if ($objField->getData($field) == $id) {
                    $model = $objField;
                    break;
                }
            }
        }

        return $model;
    }

    public function loadByName($name)
    {
        return $this->load($name, self::NAME);
    }

    public function loadStatusField()
    {
        return $this->load(self::FIELD_STATUS, self::NAME);
    }

    public function getFieldModel($fieldData)
    {
        if (is_array($fieldData) && !empty($fieldData[self::FIELD_TYPE])) {
            $fieldType = $fieldData[self::FIELD_TYPE];
        } elseif (is_string($fieldData)) {
            $fieldType = $fieldData;
        } else {
            throw new Mageplace_Backup_Exception(Mage::helper('freshdesk')->__('Invalid field type'));
        }

        $fieldType = strtolower($fieldType);
        $model     = Mage::getModel('freshdesk/field_' . $fieldType);
        if (!is_object($model)) {
            $model = Mage::getModel('freshdesk/field');
        }

        if (is_array($fieldData)) {
            $model->setData($fieldData);
        }

        return $model;
    }

    /**
     * @param null $fieldType
     *
     * @return string
     */
    public function getFieldBlockType($fieldType = null)
    {
        if ($fieldType === null) {
            $fieldType = $this->getFieldType();
        }

        return 'freshdesk/field_' . strtolower($fieldType);
    }

    public function checkFieldValue($value, &$allValues, &$skipValues)
    {
        if ($this->isRequired() && trim($value) === '') {
            throw new Mageplace_Freshdesk_Exception(Mage::helper('freshdesk')->__('Field "%s" is required', $this->getLabel()));
        }

        return true;
    }

    public function getOrderField()
    {
        if (($model = Mage::registry(self::REGISTER_ORDER_FIELD)) === null) {
            Mage::unregister(self::REGISTER_ORDER_FIELD);

            $model = false;
            try {
                if (trim(Mage::helper('freshdesk')->getOrderIdField()) != '') {
                    $field = $this->getFreshdeskModel()->getOrderField();
                    if (!empty($field) && is_array($field)) {
                        $model = $this->getFieldModel($field);
                    }
                }
            } catch (Exception $e) {
                Mage::logException($e);
            }

            Mage::register(self::REGISTER_ORDER_FIELD, $model);
        }

        return $model;
    }

    public function getLabel()
    {
        if ($this->isAdmin()) {
            return $this->getData('label');
        } else {
            return $this->getLabelInPortal();
        }
    }

    public function isRequired()
    {
        if ($this->isAdmin()) {
            return $this->getRequired();
        } else {
            return $this->getRequiredInPortal();
        }
    }

    public function isVisible()
    {
        return $this->getVisibleInPortal();
    }

    public function isEditable()
    {
        return $this->getEditableInPortal();
    }

    public function setIsAdmin($isAdmin = true)
    {
        $this->_isAdmin = $isAdmin;

        return $this;
    }

    public function isAdmin()
    {
        if ($this->_isAdmin === null) {
            return Mage::app()->getStore()->isAdmin();
        } else {
            return $this->_isAdmin;
        }
    }
}