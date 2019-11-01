<?php

namespace Bison\CustomizableProducts\Controller\Design;

use Bison\CustomizableProducts\Controller\Product;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Registry;

class Renderer extends Product
{
    const BLOCK = \Bison\CustomizableProducts\Block\Product\View::class;
    const TEMPLATE = 'Bison_CustomizableProducts::product/view/tabs.phtml';

    /** @var JsonFactory */
    protected $resultJsonFactory;

    /** @var PageFactory */
    protected $resultPageFactory;

    /**
     * Renderer constructor.
     * @param Context $context
     * @param ProductRepository $productRepository
     * @param Registry $registry
     * @param PageFactory $pageFactory
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        ProductRepository $productRepository,
        Registry $registry,
        PageFactory $pageFactory,
        JsonFactory $jsonFactory
    )
    {
        parent::__construct(
            $context,
            $productRepository,
            $registry
        );

        $this->resultPageFactory = $pageFactory;
        $this->resultJsonFactory = $jsonFactory;
    }

    /**
     * Renders tabs.phtml template and returns html result
     *
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $this->registerProduct();

        $html = $this->resultPageFactory
            ->create()
            ->getLayout()
            ->createBlock(self::BLOCK)
            ->setTemplate(self::TEMPLATE)
            ->toHtml();

        $resultJsonFactory = $this->resultJsonFactory->create();
        return $resultJsonFactory->setData($html);
    }
}