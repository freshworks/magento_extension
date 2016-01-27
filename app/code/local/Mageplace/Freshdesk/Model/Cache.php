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
 * Class Mageplace_Freshdesk_Model_Cache
 */
class Mageplace_Freshdesk_Model_Cache extends Mage_Core_Model_Abstract
{
    const CACHE_TYPE = 'freshdesk';
    const CACHE_ID   = 'freshdesk';
    const CACHE_TAG  = 'FRESHDESK';

    protected $_cacheId;
    protected $_cacheTag;

    protected function _construct()
    {
        $this->setCacheId(self::CACHE_ID);
        $this->setCacheTags(array(self::CACHE_TAG));
    }

    public function setCacheId($cacheId)
    {
        $this->_cacheId = $cacheId;
    }

    public function getCacheId()
    {
        return $this->_cacheId;
    }

    public function setCacheTags($cacheTag)
    {
        $this->_cacheTag = $cacheTag;
    }

    public function getCacheIdTags()
    {
        return false;
    }

    public function clean()
    {
        Mage::app()->getCacheInstance()->cleanType(self::CACHE_TYPE);

        return $this;
    }
}