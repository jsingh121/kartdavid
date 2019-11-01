<?php

namespace Bison\CustomizableProducts\Block\Product;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Bison\CustomizableProducts\Model\InspirationFactory;
use Bison\CustomizableProducts\Model\DesignFactory;
use Magento\Customer\Model\Session;
use Bison\CustomizableProducts\Helper\Cookie;
use Magento\Catalog\Model\Session as CatalogSession;

/**
 * Class View
 * @package Bison\CustomizableProducts\Block\Product
 */
class View extends Template
{
    /** @var Registry */
    protected $registry;

    /** @var InspirationFactory */
    protected $inspirationFactory;

    /** @var DesignFactory */
    protected $designFactory;

    /** @var Session */
    protected $customerSession;

    /** @var Cookie */
    protected $cookieHelper;

    /** @var CatalogSession */
    protected $catalogSession;

    /**
     * View constructor.
     * @param Template\Context $context
     * @param Registry $registry
     * @param InspirationFactory $inspirationFactory
     * @param Cookie $cookie
     * @param DesignFactory $designFactory
     * @param Session $session
     * @param CatalogSession $catalogSession
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        InspirationFactory $inspirationFactory,
        Cookie $cookie,
        DesignFactory $designFactory,
        Session $session,
        CatalogSession $catalogSession,
        array $data = []
    )
    {
        parent::__construct(
            $context,
            $data
        );

        $this->registry = $registry;
        $this->inspirationFactory = $inspirationFactory;
        $this->designFactory = $designFactory;
        $this->customerSession = $session;
        $this->cookieHelper = $cookie;
        $this->catalogSession = $catalogSession;
    }

    /**
     * Get inspiration images associated with current product
     *
     * @return $inspirationFactory
     */
    public function getInspirationImages()
    {
        $product = $this->registry->registry('current_product');
        $inspiration = $this->inspirationFactory->create();

        return $inspiration->getCollection()->addFieldToFilter('product_id', ['eq' => $product->getId()]);
    }

    /**
     * Get design images associated with current product and customer
     *
     * @return $designFactory
     */
    public function getDesignImages()
    {
        $product = $this->registry->registry('current_product');
        $customerId = $this->customerSession->getCustomerId();
        $design = $this->designFactory->create();

        return $design->getAssociatedDesigns($product->getId(), $customerId);
    }

    /**
     * Retrieve design data from session
     *
     * @return mixed
     */
    public function getDesignFromSession()
    {
        return $this->catalogSession->getData('design');
    }
}