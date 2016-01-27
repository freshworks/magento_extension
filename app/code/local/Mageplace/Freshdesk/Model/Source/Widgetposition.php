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
 * Class Mageplace_Freshdesk_Model_Source_Widgetposition
 */
class Mageplace_Freshdesk_Model_Source_Widgetposition extends Mageplace_Freshdesk_Model_Source_Abstract
{
    const POSITION_TOP    = 1;
    const POSITION_RIGHT  = 2;
    const POSITION_BOTTOM = 3;
    const POSITION_LEFT   = 4;

    static $POSITIONS = array(
        self::POSITION_LEFT => 'Left',
        self::POSITION_RIGHT => 'Right',
        self::POSITION_TOP => 'Top',
        self::POSITION_BOTTOM => 'Bottom',
    );

    public function toOptionArray()
    {
        $return = array();
        foreach (self::$POSITIONS as $value => $label) {
            $return[] = array('value' => $value, 'label' => $this->_getHelper()->__($label));
        }

        return $return;
    }
}
