<?php

namespace Bison\CustomizableProducts\Controller\Bodywork;

use Bison\CustomizableProducts\Helper\Bodywork;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Action;

/**
 * Class Index
 * @package Bison\CustomizableProducts\Controller\Renderer
 */
class Index extends Action
{
    /**
     * Bodywork helper
     *
     * @var Bodywork
     */
    protected $bodyworkHelper;

    /**
     * JSON result factory
     *
     * @var \Magento\Framework\Controller\Result\Json
     */
    protected $resultJsonFactory;

    /**
     * Product factory
     *
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param Bodywork $bodywork
     * @param JsonFactory $resultJsonFactory
     * @param ProductFactory $productFactory
     */
    public function __construct(
        Context $context,
        Bodywork $bodywork,
        JsonFactory $resultJsonFactory,
        ProductFactory $productFactory
    )
    {
        parent::__construct($context);
        $this->bodyworkHelper = $bodywork;
        $this->resultJsonFactory = $resultJsonFactory->create();
        $this->productFactory = $productFactory;
    }

    /**
     * Returns given product bodywork svg
     *
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $productId = $this->getRequest()->getParam('product_id', 0);
        if (!$productId) {
            return $this->resultJsonFactory->setData('');
        }

        $url = $this->getRequest()->getParam('img_url');
        if ($url) {
            $bodywork = $this->bodyworkHelper->getBodyworkByUrl($url);
        } else {
            $product = $this->productFactory->create()->load($productId);
            $bodywork = $this->bodyworkHelper->getProductBodywork($product);
        }

        return $this->resultJsonFactory->setData($bodywork);
    }
}
