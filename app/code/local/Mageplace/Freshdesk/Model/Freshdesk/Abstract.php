<?php
/**
 * Mageplace Freshdesk extension
 *
 * @category    Mageplace_Freshdesk
 * @package     Mageplace_Freshdesk
 * @copyright   Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

/**
 * Class Mageplace_Freshdesk_Model_Freshdesk_Abstract
 *
 * @method string getRequestMethod
 */
class Mageplace_Freshdesk_Model_Freshdesk_Abstract
    extends Mageplace_Freshdesk_Model_Cache
    implements Mageplace_Freshdesk_Model_Freshdesk_Interface
{
    const XML_ROOT = 'root';

    const URL_PARAM_FORMAT = 'format';

    const PARAM_ID = 'id';

    protected $_freshdesk;
    protected $_xmlRootNode;
    protected $_xmlData;
    protected $_requestData;
    protected $_helper;
    protected $_id;
    protected $_skipData = array();
    protected $_rawData = array();

    protected function _construct()
    {
        parent::_construct();

        $this->setFreshdesk(Mage::getSingleton('freshdesk/freshdesk'));
        $this->_helper = Mage::helper('freshdesk');
    }

    public function setDataFromArray(array $data)
    {
        if (array_key_exists(self::PARAM_ID, $data)) {
            $this->_id = $data[self::PARAM_ID];
        }

        $params = $this->getValidParams();
        if (empty($fields) || !is_array($fields)) {
            $this->_rawData = $data;

            return $this;
        }

        foreach ($data as $key => $value) {
            if ($key === self::PARAM_ID) {
                continue;
            }

            if (!array_key_exists($key, $params)) {
                $this->_rawData[$key] = $value;
            } else {
                $this->_skipData[$key] = $value;
            }
        }

        return $this;
    }


    /**
     * @param string $urlSuffix
     *
     * @return $this
     */
    public function setUrlSuffix($urlSuffix)
    {
        $this->getFreshdesk()->setUrlSuffix($urlSuffix);

        return $this;
    }

    /**
     * @return string
     */
    public function getUrlSuffix()
    {
        return $this->getFreshdesk()->getUrlSuffix();
    }

    /**
     * @return string
     */
    public function getUrlQueryParams()
    {
        return $this->getFreshdesk()->getUrlQueryParams();
    }

    /**
     * @param string|array $name
     * @param string|null  $value
     *
     * @return $this
     */
    public function addUrlQueryParams($name, $value = null)
    {
        $this->getFreshdesk()->addUrlQueryParams($name, $value);

        return $this;
    }

    public function getXmlRootNode()
    {
        if (is_null($this->_xmlRootNode)) {
            $this->getXmlData();
        }

        return $this->_xmlRootNode;
    }

    /**
     * @return DOMDocument
     */
    public function getXmlData()
    {
        return $this->_xmlData;
    }

    public function setXmlData($xmlData)
    {
        $this->_xmlData = $xmlData;

        return $this;
    }

    public function resetXmlData()
    {
        $this->_xmlData = null;

        return $this;
    }

    public function getRequestData()
    {
        return $this->_requestData;
    }

    public function setRequestData($requestData)
    {
        $this->_requestData = $requestData;

        return $this;
    }

    public function resetRequestData()
    {
        $this->_requestData = null;

        return $this;
    }

    public function resetUrlData()
    {
        $this->getFreshdesk()->resetUrlParams();

        return $this;
    }

    /**
     * @return $this
     */
    public function resetData()
    {
        return $this->resetXmlData()
            ->resetRequestData()
            ->resetUrlData();
    }


    /**
     * @return string
     */
    public function getRawData()
    {
        $requestData = $this->getRequestData();
        if (!empty($requestData) && is_array($requestData)) {
            return Zend_Json::encode($requestData);
        }

        $xmlData = $this->getXmlData();
        if (is_null($xmlData)) {
            return '';
        }

        $rawData = $this->getXmlData($this->getXmlRootNode())->saveXML($this->getXmlRootNode());

        return $rawData;
    }

    /**
     * @param null $method
     *
     * @return null|Zend_Http_Response
     */
    public function request($method = null)
    {
        if (is_null($method)) {
            $method = $this->getRequestMethod();
        }

        try {
            $response = $this->getFreshdesk()
                ->setRawData($this->getRawData())
                ->request($method);
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::log($this);

            return null;
        }

        return $response;
    }

    /**
     * @param Mageplace_Freshdesk_Model_Freshdesk $freshdesk
     */
    public function setFreshdesk($freshdesk)
    {
        $this->_freshdesk = $freshdesk;
    }

    /**
     * @return Mageplace_Freshdesk_Model_Freshdesk
     */
    public function getFreshdesk()
    {
        if (is_null($this->_freshdesk)) {
            $this->setFreshdesk(Mage::getSingleton('freshdesk/freshdesk'));
        }

        if (is_null($this->_freshdesk)) {
            $this->throwException('Error freshdesk model');
        }

        return $this->_freshdesk;
    }

    public function throwException($message)
    {
        Mage::exception('Mageplace_Freshdesk', $message);
    }

    protected function getValidParams()
    {
        return array();
    }

    /**
     * @return Mageplace_Freshdesk_Helper_Data
     */
    protected function _getHelper()
    {
        return $this->_helper;
    }
}