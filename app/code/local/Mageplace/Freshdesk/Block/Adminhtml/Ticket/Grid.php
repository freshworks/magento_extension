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
 * Class Mageplace_Freshdesk_Block_Adminhtml_Ticket_Grid
 */
class Mageplace_Freshdesk_Block_Adminhtml_Ticket_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_customer;

    /**
     * Constructor of Grid
     *
     */
    public function __construct()
    {
        parent::__construct();

        $collection = Mage::getResourceModel('freshdesk/ticket_collection');

        if (is_object(Mage::registry('current_customer'))) {
            $this->setCustomer(Mage::registry('current_customer'));

            $collection->setRequester($this->getCustomer()->getEmail());

            $this->setId('customer_view_orders_grid');
            $this->setDefaultSort('created_at', 'desc');
            $this->setSortable(false);
            $this->setPagerVisibility(false);
        } else {
            $this->setId('freshdesk_ticket_grid');
        }

        $this->setFilterVisibility(false);

        $this->setUseAjax(false);
        $this->setDefaultSort('display_id');
        $this->setDefaultDir('DESC');

        /* @var $collection Mageplace_Freshdesk_Model_Resource_Ticket_Collection */
        $this->setCollection($collection);

        // echo "<pre>"; var_dump($collection->getItems()); die;
    }

    /**
     * @return Mage_Customer_Model_Customer|null
     */
    public function getCustomer()
    {
        return $this->_customer;
    }

    public function setCustomer($customer)
    {
        $this->_customer = $customer;

        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/freshdesk_ticket/view', array('ticket_id' => $row->getDisplayId()));
    }

    public function getRowClickCallback()
    {
        $chooserJsObject = $this->getId();
        $js              = '
            function (grid, event) {
                var trElement = Event.findElement(event, "tr");
                if(Object.isElement(trElement) && trElement.title != "" && trElement.title != "#") {
                    window.open(trElement.title, "_blank");
                }
            }
        ';

        return $js;
    }

    public function getGridUrl()
    {
        //return $this->getUrl('*/*/index', array('_current' => true));
    }

    protected function _preparePage()
    {
        if ($this->getCustomer() !== null) {
            $this->getCollection()
                ->setPageSize(5)
                ->setCurPage(1);
        } else {
            parent::_preparePage();
        }
    }

    protected function _prepareColumns()
    {
        $this->addColumn('subject',
            array(
                'header'           => $this->__('Subject'),
                'index'            => 'subject',
                'filter'           => false,
                'column_css_class' => $this->getCustomer() !== null ? 'grid-subject-column' : ''
            )
        );

        $this->addColumn('display_id',
            array(
                'header' => $this->__('Ticket#'),
                'index'  => 'display_id',
                'type'   => 'number',
                'width'  => '80px',
                'filter' => false,
            )
        );

        $this->addColumn('created_at',
            array(
                'header'    => $this->__('Date Created'),
                'index'     => 'created_at',
                'type'      => 'datetime',
                'gmtoffset' => true,
                'width'     => '200px',
                'default'   => ' ---- ',
                'filter'    => false,
                'renderer'  => 'freshdesk/adminhtml_ticket_grid_column_renderer_datetime',
            )
        );

        $this->addColumn('requester_name',
            array(
                'header' => $this->__('Request Name'),
                'index'  => 'requester_name',
                'filter' => false,
            )
        );

        $this->addColumn('due_by',
            array(
                'header'    => $this->__('Due By'),
                'index'     => 'due_by',
                'type'      => 'datetime',
                'gmtoffset' => true,
                'width'     => '200px',
                'default'   => ' ---- ',
                'filter'    => false,
                'renderer'  => 'freshdesk/adminhtml_ticket_grid_column_renderer_datetime',
            )
        );

        $this->addColumn('priority',
            array(
                'header'  => $this->__('Priority'),
                'index'   => 'priority',
                'type'    => 'options',
                'options' => $this->_getPriority(),
            )
        );

        $this->addColumn('status',
            array(
                'header'  => $this->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => $this->_getStatus(),
            )
        );

        $this->addColumn('responder_name',
            array(
                'header' => $this->__('Agent'),
                'index'  => 'responder_name',
                'filter' => false,
                'column_css_class' => $this->getCustomer() !== null ? 'grid-agent-column' : ''
            )
        );

        $orderField = Mage::getModel('freshdesk/field')->getOrderField();
        if (is_object($orderField)) {
            $this->addColumn(Mageplace_Freshdesk_Model_Ticket::ORDER_INCREMENT_ID,
                array(
                    'header'   => $this->__('Order ID'),
                    'index'    => Mageplace_Freshdesk_Model_Ticket::ORDER_INCREMENT_ID,
                    'width'    => '80px',
                    'filter'   => false,
                    'renderer' => 'freshdesk/adminhtml_ticket_grid_column_renderer_orderincrements',
                )
            );
        }


        $this->addColumn('action',
            array(
                'header'   => $this->__('Action'),
                'index'    => 'display_id',
                'width'    => '100px',
                'type'     => 'action',
                'filter'   => false,
                'sortable' => false,
                'actions'  => array(
                    array(
                        'caption' => $this->__('View'),
                        'target'  => '_blank',
                        'url'     => $this->getUrl('*/freshdesk_ticket/view', array('ticket_id' => '$display_id')),
                    ),
                    /*array(
                        'caption' => Mage::helper('adminhtml')->__('Close'),
                        'url'     => $this->getUrl('*freshdesk_ticket/close', array(
                                    'ticket_id'   => '$display_id',
                                    'customer_id' => $this->getCustomer() !== null ? $this->getCustomer()->getId() : ''
                                )
                            ),
                    ),*/
                ),
            )
        );

        return parent::_prepareColumns();
    }

    protected function _getPriority()
    {
        return $this->getCollection()->getPriorities();
    }

    protected function _getStatus()
    {
        return $this->getCollection()->getStatuses();
    }
}
