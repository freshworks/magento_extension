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
 * Class Mageplace_Freshdesk_Block_Adminhtml_Ticket_Grid_Column_Renderer_Datetime
 */
class Mageplace_Freshdesk_Block_Adminhtml_Ticket_Grid_Column_Renderer_Datetime
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Datetime
{
    protected function _getFormat()
    {
        $format = $this->getColumn()->getFormat();
        if (!$format) {
            if (is_null(self::$_format)) {
                try {
                    self::$_format = Mage::app()->getLocale()->getDateFormat($this->getLocale()) . ' ' . Mage::app()->getLocale()->getTranslation(array('gregorian', 'short'), 'time');
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }

            $format = self::$_format;
        }

        return $format;
    }
}