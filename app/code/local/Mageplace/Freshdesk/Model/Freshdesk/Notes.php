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
 * Class Mageplace_Freshdesk_Model_Freshdesk_Notes
 *
 */
class Mageplace_Freshdesk_Model_Freshdesk_Notes extends Mageplace_Freshdesk_Model_Freshdesk_Abstract
{
    const CACHE_ID_SUFFIX_NOTE = 'note';

    const URL_NOTE_ADD = 'helpdesk/tickets/%d/conversations/note';

    const HELPDESK_NOTE = 'helpdesk_note';
    const NOTE          = 'note';

    const PARAM_TICKET_ID  = 'ticket_id';
    const PARAM_BODY       = 'body';
    const PARAM_BODY_HTML  = 'body_html';
    const PARAM_CREATED_AT = 'created_at';
    const PARAM_PRIVATE    = 'private';
    const PARAM_UPDATED_AT = 'updated_at';
    const PARAM_USER_ID    = 'user_id';

    static $VALID_PARAMS = array(
        self::PARAM_BODY,
        self::PARAM_PRIVATE,
    );

    protected $_ticketId;

    public function setDataFromArray(array $data)
    {
        if (array_key_exists(self::PARAM_TICKET_ID, $data)) {
            $this->_ticketId = (int)$data[self::PARAM_TICKET_ID];
            unset($data[self::PARAM_TICKET_ID]);
        }
        $data[self::PARAM_PRIVATE] = false;

        parent::setDataFromArray($data);

        return $this;
    }

    /**
     * @return $this
     */
    public function initData()
    {
        $requestData[self::HELPDESK_NOTE] = $this->_rawData;

        return $this->setRequestData($requestData);
    }

    public function saveNote($id = null)
    {
        if (!is_int($this->_ticketId)) {
            throw new Mageplace_Freshdesk_Exception('Wrong ticket id');
        }

        $method    = Zend_Http_Client::POST;
        $urlSuffix = sprintf(self::URL_NOTE_ADD, $this->_ticketId);

        /** @var Zend_Http_Response|null $response */
        $response = $this->resetData()
            ->initData()
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

    protected function getValidParams()
    {
        return self::$VALID_PARAMS;
    }
}