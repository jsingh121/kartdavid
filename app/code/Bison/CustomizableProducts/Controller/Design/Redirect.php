<?php

namespace Bison\CustomizableProducts\Controller\Design;

use Bison\CustomizableProducts\Controller\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Registry;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Framework\Controller\ResultFactory;
use Magento\Catalog\Model\Session;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableModel;

/**
 * Class Redirect
 * @package Bison\CustomizableProducts\Controller\Design
 */
class Redirect extends Product
{
    /** @var Configurable */
    protected $catalogProductTypeConfigurable;

    /** @var ResultFactory */
    protected $resultRedirect;

    /** @var Session */
    protected $catalogSession;

    /** @var ConfigurableModel */
    protected $configurable;

    /**
     * Redirect constructor.
     * @param Context $context
     * @param ProductRepository $productRepository
     * @param Registry $registry
     * @param Configurable $catalogProductTypeConfigurable
     * @param ResultFactory $resultFactory
     * @param Session $session
     * @param ConfigurableModel $configurable
     */
    public function __construct(
        Context $context,
        ProductRepository $productRepository,
        Registry $registry,
        Configurable $catalogProductTypeConfigurable,
        ResultFactory $resultFactory,
        Session $session,
        ConfigurableModel $configurable
    )
    {
        parent::__construct(
            $context,
            $productRepository,
            $registry
        );

        $this->catalogProductTypeConfigurable = $catalogProductTypeConfigurable;
        $this->resultRedirect = $resultFactory;
        $this->catalogSession = $session;
        $this->configurable = $configurable;
    }

    /**
     * Redirect to product url
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $this->registerProduct($this->_request->getParam('product_id'));

        $product = $this->registry->registry('product');
        $this->catalogSession->setDesign([
                'id' => $this->_request->getParam('design_id')
            ]);

        $resultRedirect = $this->resultRedirect->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($product->getUrlKey().'.html');

        return $resultRedirect;
    }
}