<?php

namespace Bison\CustomizableProducts\Observer;

use Bison\CustomizableProducts\Helper\Design;

/**
 * Class CustomerRegisterSuccess
 * @package Bison\CustomizableProducts\Observer
 */
class CustomerRegisterSuccess implements \Magento\Framework\Event\ObserverInterface
{
    /** @var Design */
    protected $designHelper;

    /**
     * CustomerLoginSuccess constructor.
     * @param Design $design
     */
    public function __construct(Design $design)
    {
        $this->designHelper = $design;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customerId = $observer->getData('customer')->getId();
        $this->designHelper->updateCustomerDesigns($customerId);
    }
}