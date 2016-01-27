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
 * Class Mageplace_Freshdesk_Model_Freshdesk
 */
class Mageplace_Freshdesk_Model_Freshdesk
{
    const DEFAULT_PASSWORD = 'X';

    const URL_SUFFIX_DASHBOARD   = 'helpdesk/dashboard';
    const URL_SUFFIX_SSO         = 'login/sso';
    const URL_SUFFIX_TICKET_EDIT = 'helpdesk/tickets/%d/edit';
    const URL_SUFFIX_TICKET_VIEW = 'helpdesk/tickets/%d';

    /**
     * @var Zend_Http_Client
     */
    protected $_httpClient;
    protected $_url;
    protected $_urlSuffix;
    protected $_urlQueryParams;
    protected $_rawData;

    public function __construct()
    {
        if (!$domain = $this->getHelper()->getDomain()) {
            Mage::exception('Mageplace_Freshdesk', $this->getHelper()->__('Error Freshdesk domain'));
        }

        if (strpos($domain, 'http://') === 0) {
            $domain = str_replace('http://', 'https://', $domain);
        } else {
            if (strpos($domain, 'https://') === false) {
                $domain = 'https://' . $domain;
            }
        }

        if (substr($domain, -1) != '/') {
            $domain = $domain . '/';
        }

        $this->setUrl($domain);
    }

    protected function _getAdapter()
    {
        $email  = $this->getHelper()->getAdminEmail();
        $apiKey = $this->getHelper()->getAdminApiKey();
        if (!$email && !$apiKey) {
            $this->throwException($this->getHelper()->__('Error Freshdesk email or API key'));
        }

        if ($apiKey) {
            $username = $apiKey;
            $password = self::DEFAULT_PASSWORD;
        } else {
            $username = $email;
            $password = $this->getHelper()->getPassword();
            if (!$password) {
                $this->throwException($this->getHelper()->__('Error Freshdesk password'));
            }
        }

        $adapter = new Zend_Http_Client_Adapter_Curl();
        $adapter->setCurlOption(CURLOPT_POST, true);
        $adapter->setCurlOption(CURLOPT_USERPWD, $username . ':' . $password);
        $adapter->setCurlOption(CURLOPT_RETURNTRANSFER, true);
        $adapter->setCurlOption(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $adapter->setCurlOption(CURLOPT_SSL_VERIFYHOST, 0);
        $adapter->setCurlOption(CURLOPT_SSL_VERIFYPEER, 0);

        return $adapter;
    }

    /**
     * @return array
     */
    protected function _getHeaders()
    {
        $headers                    = array();
        $headers['Content-Type']    = 'application/json';
        $headers['accept-encoding'] = '';

        return $headers;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->_url = strval($url);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * @param string $urlSuffix
     *
     * @return $this
     */
    public function setUrlSuffix($urlSuffix)
    {
        $this->_urlSuffix = strval($urlSuffix);

        return $this;
    }

    /**
     * @return string
     */
    public function getUrlSuffix()
    {
        return $this->_urlSuffix;
    }

    /**
     * @return string
     */
    public function getUrlQueryParams()
    {
        return empty($this->_urlQueryParams) || !is_array($this->_urlQueryParams) ? '' : '?' . http_build_query($this->_urlQueryParams);
    }

    /**
     * @return string
     */
    public function resetUrlParams()
    {
        $this->_urlSuffix = null;
        $this->_urlQueryParams = null;

        return $this;
    }

    /**
     * @param string|array $name
     * @param string|null  $value
     *
     * @return $this
     */
    public function addUrlQueryParams($name, $value = null)
    {
        if (is_array($name)) {
            $this->_urlQueryParams = array_merge($this->_urlQueryParams, $name);
        } else {
            $this->_urlQueryParams[$name] = $value;
        }

        return $this;
    }

    /**
     * @param $rawData
     *
     * @return $this
     */
    public function setRawData($rawData)
    {
        $this->_rawData = $rawData;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRawData()
    {
        return $this->_rawData;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->getUrl() . $this->getUrlSuffix() . $this->getUrlQueryParams();
    }

    public function getSSOUrl($name, $email)
    {
        $timestamp    = time();
        $secret       = $this->getHelper()->getSSOSecret();
        $to_be_hashed = $name . $email . $timestamp;
        $hash         = hash_hmac('md5', $to_be_hashed, $secret);

        $query = array(
            'name'      => $name,
            'email'     => $email,
            'timestamp' => $timestamp,
            'hash'      => $hash
        );

        return $this->getUrl() . self::URL_SUFFIX_SSO . '?' . http_build_query($query);
    }

    public function getDashboardUrl()
    {
        return $this->getUrl() . self::URL_SUFFIX_DASHBOARD;
    }

    public function getTicketEditUrl($id)
    {
        return $this->getUrl() . sprintf(self::URL_SUFFIX_TICKET_EDIT, $id);
    }

    public function getTicketViewUrl($id)
    {
        return $this->getUrl() . sprintf(self::URL_SUFFIX_TICKET_VIEW, $id);
    }

    /**
     * @return Zend_Http_Client
     */
    public function getClient()
    {
        if ($this->_httpClient === null) {
            $this->_httpClient = new Zend_Http_Client();
            $this->_httpClient->setAdapter($this->_getAdapter());
            $this->_httpClient->setHeaders($this->_getHeaders());
        }

        return $this->_httpClient;
    }

    /**
     * @param string $method
     *
     * @return Zend_Http_Response
     */
    public function request($method = Zend_Http_Client::POST)
    {
        return $this->getClient()
            ->setUri($this->getUri())
            ->setRawData($this->getRawData())
            ->request($method);
    }

    public function throwException($message)
    {
        Mage::exception('Mageplace_Freshdesk', $message);
    }

    /**
     * @return Mageplace_Freshdesk_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('freshdesk');
    }
} 