<?php

namespace Bison\CustomizableProducts\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Customer\Model\Session;
use Bison\CustomizableProducts\Model\DesignFactory;

/**
 * Class Design
 * @package Bison\CustomizableProducts\Help
 */
class Design extends AbstractHelper
{
    /** @var Cookie */
    protected $cookieHelper;

    /** @var Session */
    protected $customerSession;

    /** @var DesignFactory */
    protected $designFactory;

    /**
     * Design constructor.
     * @param Context $context
     * @param Cookie $cookie
     * @param Session $session
     * @param DesignFactory $designFactory
     */
    public function __construct(
        Context $context,
        Cookie $cookie,
        Session $session,
        DesignFactory $designFactory
    )
    {
        parent::__construct(
            $context
        );
        $this->cookieHelper = $cookie;
        $this->customerSession = $session;
        $this->designFactory = $designFactory;
    }

    /**
     *  Update customer id for each design saved in customers cookie, then remove cookie.
     *
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function updateCustomerDesigns($customerId)
    {
        $designs = $this->cookieHelper->get();

        if (!$designs) {
            return;
        }

        foreach ($this->getDesigns($designs) as $design) {
            $design->setCustomerId($customerId);
            $design->save();
        }

        $this->cookieHelper->delete();
    }

    /**
     * Get designs from user cookies
     * @param $designs
     * @return mixed
     */
    private function getDesigns($designs)
    {
        $designIds = json_decode($designs);
        $design = $this->designFactory->create();
        return $designs = $design->getCollection()->addFieldToFilter('id', ['in' => $designIds]);
    }
}