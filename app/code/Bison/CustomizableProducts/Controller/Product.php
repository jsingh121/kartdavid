<?php

namespace Bison\CustomizableProducts\Controller;

use Magento\Catalog\Controller\Product\View\ViewInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Registry;

/**
 * Class Product
 * @package Bison\CustomizableProducts\Controller
 */
abstract class Product extends \Magento\Framework\App\Action\Action implements ViewInterface
{
    /** @var ProductRepository */
    protected $productRepository;

    /** @var Registry */
    protected $registry;

    /**
     * Product constructor.
     * @param Context $context
     * @param ProductRepository $productRepository
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        ProductRepository $productRepository,
        Registry $registry
    )
    {
        parent::__construct(
            $context
        );

        $this->productRepository = $productRepository;
        $this->registry = $registry;
    }

    /**
     * Register product
     *
     * @param null $productId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function registerProduct($productId = null)
    {
        if (!$productId) {
            $productId = $this->_request->getParam('product_id');
        }

        $product = $this->productRepository->getById($productId);
        $this->registry->register('current_product', $product);
        $this->registry->register('product', $product);
    }
}
