<?php

namespace Bison\CustomizableProducts\Observer;

use Bison\CustomizableProducts\Helper\Design;

/**
 * Class CustomerLoginSuccess
 * @package Bison\CustomizableProducts\Observer
 */
class CustomerLoginSuccess implements \Magento\Framework\Event\ObserverInterface
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
        $customerId = $observer->getData('model')->getId();
        $this->designHelper->updateCustomerDesigns($customerId);
    }
}