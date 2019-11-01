<?php

namespace Bison\CustomizableProducts\Helper;

use Bison\CustomizableProducts\Model\ResourceModel\LogoCategory\CollectionFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Registry;

class PredefinedLogo extends AbstractHelper
{
    /**
     * Registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Logo category collection factory
     *
     * @var CollectionFactory
     */
    protected $logoCategoryCollectionFactory;

    /**
     * Logo collection factory
     *
     * @var \Bison\CustomizableProducts\Model\ResourceModel\PredefinedLogo\CollectionFactory
     */
    protected $logoCollectionFactory;

    /**
     * Logo helper
     *
     * @var Logo
     */
    protected $logoHelper;

    /**
     * Logo constructor.
     *
     * @param Context $context
     * @param \Magento\Framework\Registry $registry
     * @param CollectionFactory $logoCategoryCollectionFactory
     * @param \Bison\CustomizableProducts\Model\ResourceModel\PredefinedLogo\CollectionFactory $logoCollectionFactory
     * @param Logo $logoHelper
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CollectionFactory $logoCategoryCollectionFactory,
        \Bison\CustomizableProducts\Model\ResourceModel\PredefinedLogo\CollectionFactory $logoCollectionFactory,
        Logo $logoHelper
    ) {
        parent::__construct($context);

        $this->registry = $registry;
        $this->logoCategoryCollectionFactory = $logoCategoryCollectionFactory;
        $this->logoCollectionFactory = $logoCollectionFactory;
        $this->logoHelper = $logoHelper;
    }

    /**
     * Returns predefined logos available for current product
     *
     * @return array
     */
    public function getAvailableLogos()
    {
        $product = $this->registry->registry('current_product');
        $categoryId = $product->getData('logos_category');
        $categories = $this->logoCategoryCollectionFactory->create();

        $data = [];

        foreach ($categories as $category) {
            if ($category->getParentCategoryId() == $categoryId) {
                $data[$category->getId()] = [
                    'name' => $category->getName(),
                    'logos' => $this->getCategoryLogos($category->getId())
                ];
            }
        }

        return $data;
    }

    public function getCategoryLogos($categoryId)
    {
        /** @var \Bison\CustomizableProducts\Model\PredefinedLogo[] $logosCollection */
        $logosCollection = $this->logoCollectionFactory->create()
            ->addFieldToFilter('category_id', $categoryId)
            ->setOrder('name', 'asc');

        $logos = [];
        foreach ($logosCollection  as $logo)
        {
            list($width, $height) = $this->logoHelper->getLogoDimension($logo->getLogoPath());

            $logos[] = [
                'id' => $logo->getId(),
                'name' => $logo->getName(),
                'url' => $logo->getLogoUrl(),
                'width' => $width,
                'height' => $height,
            ];
        }

        return $logos;

    }

}
