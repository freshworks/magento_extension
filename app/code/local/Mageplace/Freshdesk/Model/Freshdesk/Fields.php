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
 * Class Mageplace_Freshdesk_Model_Freshdesk_Fields
 *
 */
class Mageplace_Freshdesk_Model_Freshdesk_Fields extends Mageplace_Freshdesk_Model_Freshdesk_Abstract
{
    const CACHE_ID_SUFFIX_TICKET_FIELDS = 'ticket_fields';
    const CACHE_ID_SUFFIX_ORDER_FIELD   = 'order_field';

    const URL_TICKET_FIELDS     = 'ticket_fields';
    const URL_XML_TICKET_FIELDS = 'ticket_fields.xml';

    const PARAM_FIELD_OPTIONS = "field_options";
    const PARAM_FIELD_SECTION = "section";

    const URL_PARAM_FORMAT = 'format';

    const PARAM_ID           = 'id';
    const PARAM_LABEL        = 'label';
    const PARAM_NAME         = 'name';
    const PARAM_TICKET_FIELD = 'ticket_field';
    const PARAM_ACTIVE       = 'active';
    const PARAM_FIELD_TYPE   = 'field_type';

    const FIELD_DISPLAY_ID     = 'display_id';
    const FIELD_SUBJECT        = 'subject';
    const FIELD_DESCRIPTION    = 'description';
    const FIELD_REQUESTER      = 'requester';
    const FIELD_REQUESTER_ID   = 'requester_id';
    const FIELD_EMAIL          = 'email';
    const FIELD_PRIORITY       = 'priority';
    const FIELD_PRIORITY_NAME  = 'priority_name';
    const FIELD_STATUS         = 'status';
    const FIELD_STATUS_NAME    = 'status_name';
    const FIELD_AGENT          = 'agent';
    const FIELD_RESPONDER_ID   = 'responder_id';
    const FIELD_GROUP          = 'group';
    const FIELD_GROUP_ID       = 'group_id';
    const FIELD_CUSTOM_FIELD   = 'custom_field';
    const FIELD_CREATED_AT     = 'created_at';
    const FIELD_REQUESTER_NAME = 'requester_name';

    public function getFields()
    {
        if ($this->_getData('fields') === null) {
            if (Mage::app()->useCache(self::CACHE_TYPE)) {
                if ($ticketFieldsJson = Mage::app()->loadCache($this->getCacheId() . '_' . self::CACHE_ID_SUFFIX_TICKET_FIELDS)) {
                    try {
                        $ticketFields = Zend_Json::decode($ticketFieldsJson);
                        if (!empty($ticketFields) && is_array($ticketFields)) {
                            $this->setData('fields', $ticketFields);

                            return $ticketFields;
                        }
                    } catch (Exception $e) {
                        Mage::logException($e);
                    }
                }
            }

            try {
                /** @var Zend_Http_Response|null $response */
                $response = $this->resetData()
                    ->setUrlSuffix(self::URL_TICKET_FIELDS)
                    ->addUrlQueryParams(self::URL_PARAM_FORMAT, 'json')
                    ->request(Zend_Http_Client::GET);
                if (is_null($response) || !($response instanceof Zend_Http_Response)) {
                    Mage::log($response);
                    throw new Mageplace_Freshdesk_Exception('Wrong response object');
                }

                if ($response->getStatus() != 200) {
                    Mage::log($response);
                    throw new Mageplace_Freshdesk_Exception($response->getMessage());
                }

                $ticketFields = trim($response->getRawBody());
                if (empty($ticketFields[2])) { /* if $allTicketFieldsJson == '[]' or empty */
                    Mage::log($ticketFields);
                    throw new Mageplace_Freshdesk_Exception('Wrong raw body');
                }

                if (($arrTicketFields = Zend_Json::decode($ticketFields)) && is_array($arrTicketFields)) {
                    $orderFieldMagento = $this->_getHelper()->getOrderIdField();
                    $orderField        = null;
                    $ticketFields      = array();
                    foreach ($arrTicketFields as $field) {
                        if (!empty($field[self::PARAM_TICKET_FIELD]) && !empty($field[self::PARAM_TICKET_FIELD]) && $field[self::PARAM_TICKET_FIELD][self::PARAM_ACTIVE] && $field[self::PARAM_TICKET_FIELD][self::PARAM_FIELD_OPTIONS][self::PARAM_FIELD_SECTION]!= 1) {
                            $ticketFields[strval($field[self::PARAM_TICKET_FIELD][self::PARAM_ID])] = $field[self::PARAM_TICKET_FIELD];
                            if ($orderField === null && $field[self::PARAM_TICKET_FIELD][self::PARAM_LABEL] == $orderFieldMagento) {
                                $orderField = $field[self::PARAM_TICKET_FIELD];
                            }
                        }
                    }

                    if ($orderField === null) {
                        $orderField = array();
                    }

                    if (Mage::app()->useCache(self::CACHE_TYPE)) {
                        Mage::app()->saveCache(Zend_Json::encode($ticketFields), $this->getCacheId() . '_' . self::CACHE_ID_SUFFIX_TICKET_FIELDS, $this->getCacheTags());
                        Mage::app()->saveCache(Zend_Json::encode($orderField), $this->getCacheId() . '_' . self::CACHE_ID_SUFFIX_ORDER_FIELD, $this->getCacheTags());
                    }

                    $this->setData('fields', $ticketFields);
                    $this->setData('order_field', $orderField);

                    return $ticketFields;
                }


            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
                Mage::logException($e);
            }

            $this->setData('fields', array());
            $this->setData('order_field', array());
        }

        return $this->_getData('fields');
    }

    public function getOrderField()
    {
        if ($this->_getData('order_field') === null) {
            if ($orderFieldJson = Mage::app()->loadCache($this->getCacheId() . '_' . self::CACHE_ID_SUFFIX_ORDER_FIELD)) {
                try {
                    $orderField = Zend_Json::decode($orderFieldJson);
                    if (!empty($orderField) && is_array($orderField)) {
                        $this->setData('order_field', $orderField);

                        return $orderField;
                    }
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }

            try {
                $orderFieldMagento = $this->_getHelper()->getOrderIdField();
                $orderField        = null;
                foreach ($this->getFields() as $field) {
                    if ($field[self::PARAM_LABEL] == $orderFieldMagento) {
                        $orderField = $field;
                        break;
                    }
                }

                if ($orderField === null) {
                    $orderField = array();
                }

                if (Mage::app()->useCache(self::CACHE_TYPE)) {
                    Mage::app()->saveCache(Zend_Json::encode($orderField), $this->getCacheId() . '_' . self::CACHE_ID_SUFFIX_ORDER_FIELD, $this->getCacheTags());
                }

                $this->setData('order_field', $orderField);

                return $orderField;

            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
                Mage::logException($e);
            }

            $this->setData('order_field', array());
        }

        return $this->_getData('order_field');
    }
}