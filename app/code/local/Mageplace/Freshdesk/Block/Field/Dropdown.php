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
 * Class Mageplace_Freshdesk_Block_Field_Abstract
 */
class Mageplace_Freshdesk_Block_Field_Dropdown extends Mageplace_Freshdesk_Block_Field_Abstract
{
    const CLASS_NAME_SELECT = 'select';

    public function _construct()
    {
        $this->setTemplate('freshdesk/field/dropdown.phtml');

        parent::_construct();
    }

    public function getOptions()
    {
        if ($this->_getData('choices') === null) {
            $choices[] = array(
                'label' => $this->_helper()->__('...'),
                'value' => ''
            );
            foreach ($this->getField()->getChoices() as $choice) {
                if (empty($choice)) {
                    continue;
                }

                if (!is_array($choice)) {
                    $choices[] = array(
                        'label' => strval($choice),
                        'value' => strval($choice)
                    );
                } else {
                    $choices[] = array(
                        'label' => strval($choice[0]),
                        'value' => strval($choice[1])
                    );
                }
            }

            $this->setData('choices', $choices);
        }

        return $this->_getData('choices');
    }

    protected function _beforeToHtml()
    {
        $this->addFieldClass(self::CLASS_NAME_SELECT);

        return parent::_beforeToHtml();
    }
}