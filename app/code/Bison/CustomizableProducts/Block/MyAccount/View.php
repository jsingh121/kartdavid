<?php

namespace Bison\CustomizableProducts\Block\MyAccount;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Bison\CustomizableProducts\Model\DesignFactory;
use Magento\Customer\Model\Session;
use Bison\CustomizableProducts\Helper\Cookie;

/**
 * Class View
 * @package Bison\CustomizableProducts\Block\MyAccount
 */
class View extends Template
{
    /** @var Registry */
    protected $registry;

    /** @var DesignFactory */
    protected $designFactory;

    /** @var Session */
    protected $customerSession;

    /** @var Cookie */
    protected $cookieHelper;

    /**
     * View constructor.
     * @param Template\Context $context
     * @param Registry $registry
     * @param Cookie $cookie
     * @param DesignFactory $designFactory
     * @param Session $session
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        Cookie $cookie,
        DesignFactory $designFactory,
        Session $session,
        array $data = []
    )
    {
        parent::__construct(
            $context,
            $data
        );

        $this->registry = $registry;
        $this->designFactory = $designFactory;
        $this->customerSession = $session;
        $this->cookieHelper = $cookie;
    }

    /**
     * Get design images associated with customer
     * @return $this
     */
    public function getDesignImages()
    {
        $customerId = $this->customerSession->getCustomerId();
        $design = $this->designFactory->create();

        return $design->getAssociatedDesigns(null, $customerId);
    }
}