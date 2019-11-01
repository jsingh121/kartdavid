<?php

namespace Bison\CustomizableProducts\Controller\Renderer;

use Bison\CustomizableProducts\Controller\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\Action\Context;
use Bison\CustomizableProducts\Helper\Renderer;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Registry;

/**
 * Class Index
 * @package Bison\CustomizableProducts\Controller\Renderer
 */
class Index extends Product
{
    /** @var Renderer */
    protected $rendererHelper;

    /** @var \Magento\Framework\Controller\Result\Json */
    protected $resultJsonFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param ProductRepository $productRepository
     * @param Registry $registry
     * @param Renderer $renderer
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        ProductRepository $productRepository,
        Registry $registry,
        Renderer $renderer,
        JsonFactory $resultJsonFactory
    )
    {
        parent::__construct(
            $context,
            $productRepository,
            $registry
        );

        $this->rendererHelper = $renderer;
        $this->resultJsonFactory = $resultJsonFactory->create();
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $this->registerProduct();
        $this->registerLayerId();
        $contentType = $this->_request->getParam('type');
        $html = $this->rendererHelper->getView($contentType);

        return $this->resultJsonFactory->setData($html);
    }

    /**
     * Register layer id
     */
    private function registerLayerId()
    {
        $layerId = $this->_request->getParam('layer_id');
        $this->registry->register('layer_id', $layerId);
    }
}