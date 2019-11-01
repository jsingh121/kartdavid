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
use Magento\ConfigurableProduct\Model\ConfigurableAttributeData;
use Bison\CustomizableProducts\Model\Design;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class Data extends Product
{
    const BODYWORK_ATTRIBUTE_CODE = 'bodywork';

    /** @var Configurable */
    protected $catalogProductTypeConfigurable;

    /** @var ResultFactory */
    protected $resultRedirect;

    /** @var Session */
    protected $catalogSession;

    /** @var ConfigurableModel */
    protected $configurable;

    /** @var ProductModel */
    protected $product;

    /** @var Design */
    protected $design;

    /** @var JsonFactory */
    protected $resultJsonFactory;

    /** @var JsonHelper */
    protected $jsonHelper;

    /**
     * Data constructor.
     * @param Context $context
     * @param ProductRepository $productRepository
     * @param Registry $registry
     * @param Configurable $catalogProductTypeConfigurable
     * @param ResultFactory $resultFactory
     * @param Session $session
     * @param ConfigurableModel $configurable
     * @param ConfigurableAttributeData $configurableAttributeData
     * @param ProductModel $product
     * @param Design $design
     * @param JsonFactory $jsonFactory
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        Context $context,
        ProductRepository $productRepository,
        Registry $registry,
        Configurable $catalogProductTypeConfigurable,
        ResultFactory $resultFactory,
        Session $session,
        ConfigurableModel $configurable,
        ConfigurableAttributeData $configurableAttributeData,
        ProductModel $product,
        Design $design,
        JsonFactory $jsonFactory,
        JsonHelper $jsonHelper
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
        $this->product = $product;
        $this->design = $design;
        $this->resultJsonFactory = $jsonFactory;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * Get and return design ID, attribute and option associated with given design.
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultJsonFactory = $this->resultJsonFactory->create();
        if(!$this->catalogSession->getData('design')) {
            return $resultJsonFactory;
        }

        $this->setDesign();
        $json = $this->jsonHelper->jsonEncode([
            'design_id' => $this->design->getId()
        ]);

        $this->catalogSession->unsDesign();
        return $resultJsonFactory->setData($json);
    }

    /**
     * Set design model from session
     */
    private function setDesign()
    {
        $design = $this->catalogSession->getData('design');

        if ($design && isset($design['id'])) {
            $this->design->load($design['id']);
        }
    }

    /**
     * Get simple
     *
     * @param $configurableProduct
     * @return mixed
     */
    private function getSimpleProducts($configurableProduct)
    {
        return $configurableProduct->getTypeInstance()->getUsedProducts($configurableProduct, null);

    }

    /**
     * Get product model from children.
     *
     * @return $product
     */
    private function getProduct()
    {
        $productId = $this->design->getProductId();
        $parentIds = $this->catalogProductTypeConfigurable->getParentIdsByChild($productId);

        return $this->product->load(reset($parentIds));
    }

    /**
     * Get bodywork attribute option ID for specific simple.
     * @return array
     */
    private function getBodyworkOption()
    {
        $product = $this->getProduct();
        $attributes = $product->getTypeInstance()->getConfigurableAttributes($product);
        $options = [];

        foreach ($this->getSimpleProducts($product) as $product) {
            if ($product->getId() !== $this->design->getProductId()) {
                continue;
            }
            foreach ($attributes as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                if ($productAttribute->getAttributeCode() !== self::BODYWORK_ATTRIBUTE_CODE) {
                    continue;
                }

                $productAttributeId = $productAttribute->getId();
                $options[$productAttributeId] = $product->getData($productAttribute->getAttributeCode());
            }
        }

        return $options;
    }

}