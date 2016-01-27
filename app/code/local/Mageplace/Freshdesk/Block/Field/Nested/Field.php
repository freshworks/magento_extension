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
 * Class Mageplace_Freshdesk_Block_Field_Nested_Field
 */
class Mageplace_Freshdesk_Block_Field_Nested_Field extends Mageplace_Freshdesk_Block_Field_Abstract
{
    const CLASS_NAME_INPUT_TEXT = 'select';

    public function _construct()
    {
        $this->setTemplate('freshdesk/field/nested_field.phtml');

        parent::_construct();
    }

    public function getNestedFields()
    {
        if ($this->_getData('nested_ticket_fields') === null) {
            $this->setData('nested_ticket_fields', $this->getField()->getNestedTicketFields());
        }

        return $this->_getData('nested_ticket_fields');
    }

    public function getFirstLevelNestedField()
    {
        return $this->getField()->getFirstLevelField();
    }

    public function getFirstLevelNestedFieldName()
    {
        return $this->getField()->getFirstLevelFieldName();
    }

    public function getFirstLevelNestedFieldId()
    {
        return $this->getFirstLevelNestedFieldName() . '_' . $this->getField()->getFirstLevelFieldId();
    }

    public function getFirstLevelNestedFieldLabel()
    {
        return $this->getField()->getFirstLevelFieldLabel();
    }

    public function hasSecondLevel()
    {
        return $this->getField()->hasSecondLevel();
    }

    public function getSecondLevelNestedField()
    {
        return $this->getField()->getSecondLevelField();
    }

    public function getSecondLevelNestedFieldName()
    {
        return $this->getField()->getSecondLevelFieldName();
    }

    public function getSecondLevelNestedFieldId()
    {
        return $this->getSecondLevelNestedFieldName() . '_' . $this->getField()->getSecondLevelFieldId();
    }

    public function getSecondLevelNestedFieldLabel()
    {
        return $this->getField()->getSecondLevelFieldLabel();
    }


    public function getNestedOptions()
    {
        if ($this->_getData('nested_choices') === null) {
            $options = $this->getNestedOptionChilds($this->getField()->getNestedChoices());
            $this->setData('nested_choices', $options);
        }

        return $this->_getData('nested_choices');
    }

    protected function getNestedOptionChilds(array $array)
    {
        $choices[] = array(
            'label'    => $this->_helper()->__('...'),
            'value'    => '',
            'children' => array()
        );

        foreach ($array as $choice) {
            if (empty($choice)) {
                continue;
            }

            if (!is_array($choice)) {
                $choices[] = array(
                    'label'    => strval($choice),
                    'value'    => strval($choice),
                    'children' => array()
                );
            } else {
                $choices[] = array(
                    'label'    => strval($choice[0]),
                    'value'    => strval($choice[1]),
                    'children' => empty($choice[2]) || !is_array($choice[2]) ? array() : $this->getNestedOptionChilds($choice[2])
                );
            }

        }

        return $choices;
    }

    protected function _beforeToHtml()
    {
        $this->addFieldClass(self::CLASS_NAME_INPUT_TEXT);

        return parent::_beforeToHtml();
    }
}