<?php

namespace Bison\CustomizableProducts\Observer;

use Magento\Catalog\Model\Product;

/**
 * Class LayoutLoadBefore
 * @package Bison\CustomizableProducts\Observer
 */
class LayoutLoadBefore implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    public function __construct(
        \Magento\Framework\Registry $registry
    )
    {
        $this->_registry = $registry;
    }

    /**
     * Update layout for customizable products
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var Product $product */
        $product = $this->_registry->registry('current_product');

        if (!$product) {
            return $this;
        }

        if ($product->getIsCustomizable()) {
            $layout = $observer->getLayout();
            $layout->getUpdate()->addHandle('catalog_customizable_product_view');
        }

        return $this;
    }
}