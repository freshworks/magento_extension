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
 * Class Mageplace_Freshdesk_Model_Field_Nested_Field
 *
 * @method array getNestedTicketFields
 * @method array getNestedChoices
 */
class Mageplace_Freshdesk_Model_Field_Nested_Field extends Mageplace_Freshdesk_Model_Field
{
    /**
     * @return Varien_Object
     * @throws Mageplace_Freshdesk_Exception
     */
    public function getFirstLevelField()
    {
        if ($this->_getData('nested_ticket_fields_level_1') === null) {
            $fields = $this->getNestedTicketFields();
            if (empty($fields[0])) {
                throw new Mageplace_Freshdesk_Exception('Error nested ticket fields');
            }

            $this->setData('nested_ticket_fields_level_1', new Varien_Object($fields[0]));
        }

        return $this->_getData('nested_ticket_fields_level_1');
    }

    public function getFirstLevelFieldName()
    {
        return $this->getFirstLevelField()->getName();
    }

    public function getFirstLevelFieldId()
    {
        return $this->getFirstLevelField()->getId();
    }

    public function getFirstLevelFieldLabel()
    {
        if ($this->isAdmin()) {
            return $this->getFirstLevelField()->getLabel();
        } else {
            return $this->getFirstLevelField()->getLabelInPortal();
        }
    }

    public function getSecondLevelField()
    {
        if ($this->_getData('nested_ticket_fields_level_2') === null) {
            $fields = $this->getNestedTicketFields();
            if (empty($fields[1])) {
                $this->setData('nested_ticket_fields_level_2', false);
            } else {
                $this->setData('nested_ticket_fields_level_2', new Varien_Object($fields[1]));
            }
        }

        return $this->_getData('nested_ticket_fields_level_2');
    }

    public function hasSecondLevel()
    {
        return is_object($this->getSecondLevelField());
    }

    public function getSecondLevelFieldName()
    {
        if (!$this->hasSecondLevel()) {
            return '';
        }

        return $this->getSecondLevelField()->getName();
    }

    public function getSecondLevelFieldId()
    {
        if (!$this->hasSecondLevel()) {
            return '';
        }

        return $this->getSecondLevelField()->getId();
    }

    public function getSecondLevelFieldLabel()
    {
        if (!$this->hasSecondLevel()) {
            return '';
        }

        if ($this->isAdmin()) {
            return $this->getSecondLevelField()->getLabel();
        } else {
            return $this->getSecondLevelField()->getLabelInPortal();
        }
    }


    public function checkFieldValue($value, &$allValues, &$skipValues)
    {
        parent::checkFieldValue($value, $allValues, $skipValues);

        if ($value !== '') {
            $check             = false;
            $approveSkipValues = array();
            $choices           = $this->getNestedChoices();
            foreach ($choices as $level0) {
                if ($level0[1] == $value) {
                    if (empty($level0[2])) {
                        $check = true;
                    } else {
                        if (empty($skipValues[$this->getFirstLevelFieldName()])) {
                            if (!$this->isRequired()) {
                                $check = true;
                            }

                            break;
                        }

                        $level1value = $skipValues[$this->getFirstLevelFieldName()];
                        if (trim($level1value) === '') {
                            break;
                        } else {
                            foreach ($level0[2] as $level1) {
                                if ($level1[1] == $level1value) {
                                    $approveSkipValues[] = $this->getFirstLevelFieldName();

                                    if (empty($level1[2])) {
                                        $check = true;
                                    } else {
                                        if (empty($skipValues[$this->getSecondLevelFieldName()])) {
                                            if (!$this->isRequired()) {
                                                $check = true;
                                            }

                                            break 2;
                                        }

                                        $level2value = $skipValues[$this->getSecondLevelFieldName()];
                                        if (trim($level2value) !== '') {
                                            foreach ($level1[2] as $level2) {
                                                if ($level2[1] == $level2value) {
                                                    $approveSkipValues[] = $this->getSecondLevelFieldName();

                                                    $check = true;

                                                    break 3;
                                                }
                                            }
                                        }

                                    }

                                    break 2;
                                }
                            }
                        }
                    }

                    break;
                }
            }

            if (!$check) {
                throw new Mageplace_Freshdesk_Exception(Mage::helper('freshdesk')->__('Field "%s" is not valid, please re-enter', $this->getLabel()));
            }
        }

        if(!empty($approveSkipValues)) {
            return $approveSkipValues;
        }

        return true;
    }
}