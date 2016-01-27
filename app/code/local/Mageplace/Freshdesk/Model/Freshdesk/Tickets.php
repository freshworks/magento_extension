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
 * Class Mageplace_Freshdesk_Model_Freshdesk_Tickets
 *
 * @method Mageplace_Freshdesk_Model_Freshdesk_Tickets setSubject
 * @method Mageplace_Freshdesk_Model_Freshdesk_Tickets setDescription
 * @method Mageplace_Freshdesk_Model_Freshdesk_Tickets setEmail
 * @method Mageplace_Freshdesk_Model_Freshdesk_Tickets setPriority
 * @method Mageplace_Freshdesk_Model_Freshdesk_Tickets setStatus
 * @method string getSubject
 * @method string getDescription
 * @method string getEmail
 */
class Mageplace_Freshdesk_Model_Freshdesk_Tickets extends Mageplace_Freshdesk_Model_Freshdesk_Abstract
{
    const CACHE_ID_SUFFIX_TICKETS = 'tickets';
    const CACHE_ID_SUFFIX_TICKET  = 'ticket';

    const TICKETS_MAX_PAGES = 1000;

    const URL_TICKETS      = 'helpdesk/tickets/filter/all_tickets';
    const URL_XML_TICKETS  = 'helpdesk/tickets.xml';
    const URL_JSON_TICKETS = 'helpdesk/tickets.json';
    const URL_JSON_TICKET  = 'helpdesk/tickets/%d';

    const URL_PARAM_PAGE      = 'page';
    const URL_PARAM_EMAIL     = 'email';
    const URL_PARAM_REQUESTER = 'requester';

    const FIELD_DISPLAY_ID    = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_DISPLAY_ID;
    const FIELD_SUBJECT       = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_SUBJECT;
    const FIELD_CREATED_AT    = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_CREATED_AT;
    const FIELD_DESCRIPTION   = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_DESCRIPTION;
    const FIELD_REQUESTER     = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_REQUESTER;
    const FIELD_EMAIL         = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_EMAIL;
    const FIELD_PRIORITY      = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_PRIORITY;
    const FIELD_PRIORITY_NAME = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_PRIORITY_NAME;
    const FIELD_STATUS        = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_STATUS;
    const FIELD_STATUS_NAME   = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_STATUS_NAME;
    const FIELD_AGENT         = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_AGENT;
    const FIELD_RESPONDER_ID  = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_RESPONDER_ID;
    const FIELD_GROUP         = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_GROUP;
    const FIELD_GROUP_ID      = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_GROUP_ID;
    const FIELD_CUSTOM_FIELD  = Mageplace_Freshdesk_Model_Freshdesk_Fields::FIELD_CUSTOM_FIELD;

    const FIELD_NESTED_NAME = 'name';

    const HELPDESK_TICKET = 'helpdesk_ticket';

    const XML_RESPONSE_HELPDESK_TICKET  = 'helpdesk-ticket';
    const XML_RESPONSE_HELPDESK_TICKETS = 'helpdesk-tickets';

    const STATUS_CLOSE = 5;

    static $VALID_FIELDS_NAMES = array(
        self::FIELD_EMAIL,
        self::FIELD_RESPONDER_ID
    );

    static $CHANGE_FIELDS_NAMES = array(
        self::FIELD_AGENT => self::FIELD_RESPONDER_ID,
        self::FIELD_GROUP => self::FIELD_GROUP_ID
    );

    static $FIELDS_TYPES = array(
        'default_requester'   => '',
        'default_subject'     => '',
        'default_ticket_type' => '',
        'default_source'      => '',
        'default_status'      => '',
        'default_priority'    => '',
        'default_group'       => '',
        'default_agent'       => '',
        'default_description' => '',
        'custom_text'         => '',
        'custom_dropdown'     => '',
        'nested_field'        => '',
        'custom_paragraph'    => '',
        'custom_number'       => '',
        'custom_checkbox'     => '',
    );

    /**
     * @var int
     */
    protected $_displayId;

    /**
     * @var array
     */
    protected $_rawData = array();

    /**
     * @var Mageplace_Freshdesk_Model_Resource_Field_Collection
     */
    protected $_fieldsCollection;

    /**
     * @var array
     */
    protected $_fields;

    /**
     * @var array
     */
    protected $_skipFields = array();

    /**
     * @var array
     */
    protected $_notSkipFields = array();


    protected function _construct()
    {
        parent::_construct();

        $this->_fieldsCollection = Mage::getResourceModel('freshdesk/field_collection');
        if ($this->_fieldsCollection->getSize() < 1) {
            throw new Mageplace_Freshdesk_Exception($this->_helper->__("Can't get ticket fields"), Mageplace_Freshdesk_Exception::ERROR_FIELDS);
        }

        $this->_fields = $this->_fieldsCollection->getItemsByNames();
    }

    public function getFieldsCollection()
    {
        return $this->_fieldsCollection;
    }

    public function getFields()
    {
        return $this->_fields;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setTicketFromArray(array $data)
    {
        $fields = $this->getFields();
        foreach ($data as $key => $value) {
            if ($key === self::FIELD_DISPLAY_ID) {
                $this->_displayId = (int)$value;
                continue;
            }

            if (!in_array($key, self::$VALID_FIELDS_NAMES) && !array_key_exists($key, $fields)) {
                $this->_skipFields[$key] = $value;
                continue;
            }

            if (in_array($key, self::$VALID_FIELDS_NAMES) || stripos($fields[$key]->getFieldType(), 'default_') !== false) {
                $this->_rawData[$key] = $value;
            } else {
                $this->_rawData[self::FIELD_CUSTOM_FIELD][$key] = $value;
            }
        }

        return $this;
    }

    public function getDefaultPriority()
    {
        return $this->_getHelper()->getPriorityDefault();
    }

    public function getDefaultStatus()
    {
        return $this->_getHelper()->getStatusDefault();
    }

    public function getStatusClose()
    {
        return $this->_getHelper()->getStatusClose();
    }

    public function getXmlRootNodeName()
    {
        return self::HELPDESK_TICKET;
    }

    public function checkData($key, $value)
    {
        if ($key == self::FIELD_EMAIL) {
            if (!Zend_Validate::is($value, 'EmailAddress')) {
                throw new Mageplace_Freshdesk_Exception(Mage::helper('freshdesk')->__('Field "%s" is not valid, please re-enter', $key));
            }

            return true;
        }

        if (empty($this->_fields[$key])) {
            throw new Mageplace_Freshdesk_Exception(Mage::helper('freshdesk')->__('Field "%s" is not valid, please re-enter', $key));
        }

        /** @var Mageplace_Freshdesk_Model_Field|mixed $field */
        $field = $this->_fields[$key];

        $value = trim($value);
        if ($field->isRequired() && $value === '') {
            throw new Mageplace_Freshdesk_Exception(Mage::helper('freshdesk')->__('Field "%s" is required', $field->getLabel()));
        }

        $checkedFieldValue = $field->checkFieldValue($value, $this->_rawData, $this->_skipFields);
        if (is_array($checkedFieldValue)) {
            $this->_notSkipFields = array_merge($this->_notSkipFields, $checkedFieldValue);
        } elseif (!$checkedFieldValue) {
            throw new Mageplace_Freshdesk_Exception(Mage::helper('freshdesk')->__('Field "%s" is not valid, please re-enter', $field->getLabel()));
        }

        return true;
    }

    public function prepareData()
    {
        foreach ($this->_rawData as $key => $value) {
            if (is_array($value)) {
                foreach ($this->_rawData[$key] as $aKey => $aValue) {
                    $this->checkData($aKey, $aValue);
                    if(trim($aValue) === '') {
                        unset($this->_rawData[$key][$aKey]);
                    }
                }

                if(empty($this->_rawData[$key])) {
                    unset($this->_rawData[$key]);
                }
            } else {
                $this->checkData($key, $value);
                if(trim($value) === '') {
                    unset($this->_rawData[$key]);
                }
            }
        }

        foreach ($this->_notSkipFields as $notSkipField) {
            if (array_key_exists($notSkipField, $this->_skipFields)) {
                $this->_rawData[self::FIELD_CUSTOM_FIELD][$notSkipField] = $this->_skipFields[$notSkipField];
                unset($this->_skipFields[$notSkipField]);
            }
        }

        foreach (self::$CHANGE_FIELDS_NAMES as $current => $change) {
            if (array_key_exists($current, $this->_rawData)) {
                $this->_rawData[$change] = $this->_rawData[$current];
                unset($this->_rawData[$current]);
            } elseif (!empty($this->_rawData[self::FIELD_CUSTOM_FIELD]) && array_key_exists($current, $this->_rawData[self::FIELD_CUSTOM_FIELD])) {
                $this->_rawData[self::FIELD_CUSTOM_FIELD][$change] = $this->_rawData[self::FIELD_CUSTOM_FIELD][$current];
                unset($this->_rawData[self::FIELD_CUSTOM_FIELD][$current]);
            }
        }

        if (empty($this->_rawData[self::FIELD_EMAIL])
            && !empty($this->_rawData[self::FIELD_REQUESTER])
            && strpos($this->_rawData[self::FIELD_REQUESTER], '@') > 0

        ) {
            $this->_rawData[self::FIELD_EMAIL] = $this->_rawData[self::FIELD_REQUESTER];
            unset($this->_rawData[self::FIELD_REQUESTER]);
        }
    }

    /**
     * @return $this
     */
    public function initTicketData()
    {
        $this->prepareData();

        $requestData[self::HELPDESK_TICKET] = $this->_rawData;

        return $this->setRequestData($requestData);
    }

    /**
     * @return $this
     */
    public function initTicketXmlData()
    {
        $this->prepareData();

        $this->_xmlData = new DOMDocument();

        $this->_xmlRootNode = $this->_xmlData->createElement($this->getXmlRootNodeName());

        foreach ($this->_rawData as $key => $value) {
            $value     = strval($value);
            $paramNode = $this->_xmlData->createElement($key, $value);
            $this->_xmlRootNode->appendChild($paramNode);
        }

        $this->_xmlData->appendChild($this->_xmlRootNode);

        return $this;
    }

    public function createTicket($id = null)
    {
        if ($id > 0) {
            $method    = Zend_Http_Client::PUT;
            $urlSuffix = sprintf(self::URL_JSON_TICKET, $id);
        } elseif ($this->_displayId > 0) {
            $method    = Zend_Http_Client::PUT;
            $urlSuffix = sprintf(self::URL_JSON_TICKET, $this->_displayId);
        } else {
            $method    = Zend_Http_Client::POST;
            $urlSuffix = self::URL_JSON_TICKETS;
        }

        /** @var Zend_Http_Response|null $response */
        $response = $this->resetData()
            ->initTicketData()
            ->setUrlSuffix($urlSuffix)
            ->addUrlQueryParams(self::URL_PARAM_FORMAT, 'json')
            ->request($method);

        if (is_null($response) || !($response instanceof Zend_Http_Response)) {
            Mage::log($response);
            throw new Mageplace_Freshdesk_Exception('Wrong response object');
        }

        switch ($response->getStatus()) {
            case 200:
            case 201:
                return true;

            default:
                Mage::log($this);
                throw new Mageplace_Freshdesk_Exception($response->getMessage());
        }
    }

    public function getTickets($requester = null)
    {
        if (Mage::app()->useCache(self::CACHE_TYPE)) {
            if ($ticketsJson = Mage::app()->loadCache($this->getCacheId() . '_' . self::CACHE_ID_SUFFIX_TICKETS . (null !== $requester ? '_' . $requester : ''))) {
                try {
                    $tickets = Zend_Json::decode($ticketsJson);
                    if (!empty($tickets) && is_array($tickets)) {
                        return $tickets;
                    }
                } catch (Exception $e) {
                    Mage::logException($e);
                    Mage::app()->removeCache(self::CACHE_ID . '_' . self::CACHE_ID_SUFFIX_TICKETS . (null !== $requester ? '_' . $requester : ''));
                }
            }
        }

        try {
            $page    = 1;
            $allJson = array();
            do {
                $this->resetData()
                    ->setUrlSuffix(self::URL_TICKETS)
                    ->addUrlQueryParams(self::URL_PARAM_FORMAT, 'json')
                    ->addUrlQueryParams(self::URL_PARAM_PAGE, $page++);

                if ($requester !== null) {
                    if (is_int($requester)) {
                        $this->addUrlQueryParams(self::URL_PARAM_REQUESTER, $requester);
                    } else {
                        $this->addUrlQueryParams(self::URL_PARAM_EMAIL, $requester);
                    }
                }

                /** @var Zend_Http_Response|null $response */
                $response = $this->request(Zend_Http_Client::GET);

                if (is_null($response) || !($response instanceof Zend_Http_Response)) {
                    Mage::log($response);
                    throw new Mageplace_Freshdesk_Exception('Wrong response object');
                }

                if ($response->getStatus() != 200) {
                    Mage::log($response);
                    throw new Mageplace_Freshdesk_Exception($response->getMessage());
                }

                $json = trim($response->getRawBody());
                if(strpos($json, '"errors"') !== false) {
                    $error = Zend_Json::decode($json);
                    if(!empty($error['errors'])) {
                        Mage::logException(new Mageplace_Freshdesk_Exception($json));
                        break;
                    }
                }

                if (empty($json[2])) { /* For example if $json == '[]' */
                    break;
                }

                $allJson [] = substr($json, 1, -1); /* Cut first([) and last(]) characters  */

            } while ($json && $page <= self::TICKETS_MAX_PAGES);

            $allTicketsJson = '[' . implode(',', $allJson) . ']';
            if (Mage::app()->useCache(self::CACHE_TYPE)) {
                Mage::app()->saveCache(
                    $allTicketsJson,
                    $this->getCacheId() . '_' . self::CACHE_ID_SUFFIX_TICKETS . (null !== $requester ? '_' . $requester : ''),
                    $this->getCacheTags()
                );
            }

            return Zend_Json::decode($allTicketsJson);

        } catch (Exception $e) {
            Mage::logException($e);
        }

        return array();
    }

    public function getTicket($id, $requester = null)
    {
        if (Mage::app()->useCache(self::CACHE_TYPE)) {
            if ($ticketJson = Mage::app()->loadCache($this->getCacheId() . '_' . self::CACHE_ID_SUFFIX_TICKET . (null !== $requester ? '_' . $requester : ''))) {
                try {
                    $ticket = Zend_Json::decode($ticketJson);
                    if (!empty($ticket[$id]) && is_array($ticket[$id])) {
                        if (!empty($ticket[$id][self::HELPDESK_TICKET])) {
                            return $ticket[$id][self::HELPDESK_TICKET];
                        }

                        return $ticket[$id];
                    }
                } catch (Exception $e) {
                    Mage::logException($e);
                    Mage::log($ticketJson);
                    Mage::app()->removeCache($this->getCacheId() . '_' . self::CACHE_ID_SUFFIX_TICKET . (null !== $requester ? '_' . $requester : ''));
                }
            }
        }

        try {
            $this->resetData()
                ->setUrlSuffix(sprintf(self::URL_JSON_TICKET, $id))
                ->addUrlQueryParams(self::URL_PARAM_FORMAT, 'json');

            /** @var Zend_Http_Response|null $response */
            $response = $this->request(Zend_Http_Client::GET);

            if (is_null($response) || !($response instanceof Zend_Http_Response)) {
                Mage::log($response);
                throw new Mageplace_Freshdesk_Exception('Wrong response object');
            }

            if ($response->getStatus() != 200) {
                Mage::log($response);
                throw new Mageplace_Freshdesk_Exception($response->getMessage());
            }

            $json   = trim($response->getRawBody());
            $ticket = Zend_Json::decode($json);
            $ticket = $ticket[self::HELPDESK_TICKET];

            if (Mage::app()->useCache(self::CACHE_TYPE)) {
                $json = Zend_Json::encode($ticket);

                $ticketJson = Mage::app()->loadCache($this->getCacheId() . '_' . self::CACHE_ID_SUFFIX_TICKET . (null !== $requester ? '_' . $requester : ''));
                if (empty($ticketJson[2])) { /* For example if $json == '{}' */
                    $ticketJson = '{"' . $id . '":' . $json . '}';
                } else {
                    $ticketJson = substr($ticketJson, 0, -1); /* Cut last (}) characters  */
                    $ticketJson .= ',' . '"' . $id . '":' . $json . '}';
                }

                Mage::app()->saveCache(
                    $ticketJson,
                    $this->getCacheId() . '_' . self::CACHE_ID_SUFFIX_TICKET . (null !== $requester ? '_' . $requester : ''),
                    $this->getCacheTags()
                );
            }

            return $ticket;

        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            Mage::logException($e);
        }

        return array();
    }

    static public function cleanCache($requester = null)
    {
        Mage::app()->removeCache(self::CACHE_ID . '_' . self::CACHE_ID_SUFFIX_TICKET . (null !== $requester ? '_' . $requester : ''));
        Mage::app()->removeCache(self::CACHE_ID . '_' . self::CACHE_ID_SUFFIX_TICKETS . (null !== $requester ? '_' . $requester : ''));
    }
}