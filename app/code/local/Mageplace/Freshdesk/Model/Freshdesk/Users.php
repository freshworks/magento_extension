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
 * Class Mageplace_Freshdesk_Model_Freshdesk_Users
 *
 */
class Mageplace_Freshdesk_Model_Freshdesk_Users extends Mageplace_Freshdesk_Model_Freshdesk_Abstract
{
    const CACHE_ID_SUFFIX_USER          = 'user';
    const CACHE_ID_SUFFIX_USER_BY_EMAIL = 'user_email';

    const USER = 'user';

    const URL_CONTACT       = 'contacts/%d';
    const URL_CONTACTS      = 'contacts';
    const URL_CONTACTS_JSON = 'contacts.json';

    const URL_PARAM_QUERY = 'query';

    const PARAM_EMAIL     = 'email';
    const PARAM_LANGUAGE  = 'language';
    const PARAM_MOBILE    = 'mobile';
    const PARAM_NAME      = 'name';
    const PARAM_PHONE     = 'phone';
    const PARAM_TIME_ZONE = 'time_zone';

    static $VALID_PARAMS = array(
        self::PARAM_EMAIL,
        self::PARAM_LANGUAGE,
        self::PARAM_MOBILE,
        self::PARAM_NAME,
        self::PARAM_PHONE,
        self::PARAM_TIME_ZONE,
    );

    public function getUser($userId)
    {
        $userId = trim($userId);
        if (!$userId) {
            return array();
        }

        if (null === $this->_getData('user_' . $userId)) {
            $isEmail = strpos($userId, '@') > 0;

            if (Mage::app()->useCache(self::CACHE_TYPE)) {
                if ($isEmail) {
                    $userJson = Mage::app()->loadCache($this->getCacheId() . '_' . self::CACHE_ID_SUFFIX_USER_BY_EMAIL);
                } else {
                    $userJson = Mage::app()->loadCache($this->getCacheId() . '_' . self::CACHE_ID_SUFFIX_USER);
                }

                if ($userJson) {
                    try {
                        $user = Zend_Json::decode($userJson);
                        if (!empty($user[$userId]) && is_array($user[$userId])) {
                            $this->setData('user_' . $user[$userId][self::PARAM_ID], $user[$userId]);
                            $this->setData('user_' . $user[$userId][self::PARAM_EMAIL], $user[$userId]);

                            return $user[$userId];
                        }

                        unset($user);
                    } catch (Exception $e) {
                        Mage::logException($e);
                        Mage::app()->removeCache(self::CACHE_ID . '_' . self::CACHE_ID_SUFFIX_USER);
                        Mage::app()->removeCache(self::CACHE_ID . '_' . self::CACHE_ID_SUFFIX_USER_BY_EMAIL);
                    }
                }

                unset($userJson);
            }

            try {
                /** @var Zend_Http_Response|null $response */
                $this->resetData();

                if ($isEmail) {
                    $this->setUrlSuffix(self::URL_CONTACTS);
                    $this->addUrlQueryParams(self::URL_PARAM_QUERY, 'email is ' . $userId);
                } else {
                    $this->setUrlSuffix(sprintf(self::URL_CONTACT, $userId));
                }

                $this->addUrlQueryParams(self::URL_PARAM_FORMAT, 'json');

                $response = $this->request(Zend_Http_Client::GET);
                if (is_null($response) || !($response instanceof Zend_Http_Response)) {
                    Mage::log($response);
                    throw new Mageplace_Freshdesk_Exception('Wrong response object');
                }

                if ($response->getStatus() != 200) {
                    Mage::log($response);
                    throw new Mageplace_Freshdesk_Exception($response->getMessage());
                }

                $userJson = trim($response->getRawBody());
                if (empty($userJson[2])) { /* if $allTicketFieldsJson == '{}' or empty */
                    $this->setData('user_' . $userId, array());

                    return array();
                }

                $user = Zend_Json::decode($userJson);
                if (is_array($user) && empty($user[self::USER])) {
                    $user = array_shift($user);
                }

                if (!empty($user[self::USER])) {
                    $user = $user[self::USER];
                }

                if (!array_key_exists(self::PARAM_ID, $user) || !array_key_exists(self::PARAM_EMAIL, $user)) {
                    throw new Mageplace_Freshdesk_Exception('Wrong user data');
                }

                if (Mage::app()->useCache(self::CACHE_TYPE)) {
                    $userJson = Zend_Json::encode($user);

                    $userJsonCached = Mage::app()->loadCache($this->getCacheId() . '_' . self::CACHE_ID_SUFFIX_USER);
                    if (empty($userJsonCached[2])) { /* For example if $json == '{}' */
                        $userJsonCached = '{"' . $user[self::PARAM_ID] . '":' . $userJson . '}';
                    } else {
                        $userJsonCached = substr($userJsonCached, 0, -1); /* Cut last (}) characters  */
                        $userJsonCached .= ',' . '"' . $user[self::PARAM_ID] . '":' . $userJson . '}';
                    }
                    Mage::app()->saveCache($userJsonCached, $this->getCacheId() . '_' . self::CACHE_ID_SUFFIX_USER, $this->getCacheTags());

                    $userJsonCached = Mage::app()->loadCache($this->getCacheId() . '_' . self::CACHE_ID_SUFFIX_USER_BY_EMAIL);
                    if (empty($userJsonCached[2])) { /* For example if $json == '{}' */
                        $userJsonCached = '{"' . $user[self::PARAM_EMAIL] . '":' . $userJson . '}';
                    } else {
                        $userJsonCached = substr($userJsonCached, 0, -1); /* Cut last (}) characters  */
                        $userJsonCached .= ',' . '"' . $user[self::PARAM_EMAIL] . '":' . $userJson . '}';
                    }
                    Mage::app()->saveCache($userJsonCached, $this->getCacheId() . '_' . self::CACHE_ID_SUFFIX_USER_BY_EMAIL, $this->getCacheTags());
                }

                $this->setData('user_' . $user[self::PARAM_ID], $user);
                $this->setData('user_' . $user[self::PARAM_EMAIL], $user);

                return $user;
            } catch (Exception $e) {
#                Mage::getSingleton('core/session')->addError($e->getMessage());
                Mage::logException($e);
            }

            $this->setData('user_' . $userId, array());
        }

        return $this->_getData('user_' . $userId);
    }

    /**
     * @return $this
     */
    public function initData()
    {
        $requestData[self::USER] = $this->_rawData;

        return $this->setRequestData($requestData);
    }

    public function saveUser($id = null)
    {
        $this->resetData()
            ->initData();

        if ($id > 0) {
            $method = Zend_Http_Client::PUT;

            $this->setUrlSuffix(sprintf(self::URL_CONTACT, $id))
                ->addUrlQueryParams(self::URL_PARAM_FORMAT, 'json');

        } elseif ($this->_id > 0) {
            $method = Zend_Http_Client::PUT;

            $this->setUrlSuffix(sprintf(self::URL_CONTACT, $this->_id))
                ->addUrlQueryParams(self::URL_PARAM_FORMAT, 'json');
        } else {
            $method = Zend_Http_Client::POST;

            $this->setUrlSuffix(self::URL_CONTACTS_JSON);
        }

        /** @var Zend_Http_Response|null $response */
        $response = $this->request($method);
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

    protected function getValidParams()
    {
        return self::$VALID_PARAMS;
    }

    static public function cleanCache()
    {
        Mage::app()->removeCache(self::CACHE_ID . '_' . self::CACHE_ID_SUFFIX_USER);
        Mage::app()->removeCache(self::CACHE_ID . '_' . self::CACHE_ID_SUFFIX_USER_BY_EMAIL);
    }
}