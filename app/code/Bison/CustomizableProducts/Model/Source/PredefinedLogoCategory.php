<?php

namespace Bison\CustomizableProducts\Model\Source;

use Bison\CustomizableProducts\Model\ResourceModel\LogoCategory\CollectionFactory;

class PredefinedLogoCategory implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Logo category collection factory
     *
     * @var CollectionFactory
     */
    protected $logoCategoryCollectionFactory;

    public function __construct(CollectionFactory $logoCategoryFactory)
    {
        $this->logoCategoryCollectionFactory = $logoCategoryFactory;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $data = [];

        $collection = $this->logoCategoryCollectionFactory->create();

        foreach ($collection as $category) {
            if ($category->getParentCategoryId() == 0) {
                $data[] = [
                    'label' => $category->getName(),
                    'value' => $this->getSubcategoriesList($category->getId()),

                ];
            }
        }

        return $data;
    }

    /**
     * Returns a list of subcategories
     *
     * @param $categoryId
     * @return array
     */
    public function getSubcategoriesList($categoryId)
    {
        $data = [];
        $collection = $this->logoCategoryCollectionFactory->create();

        foreach ($collection as $category) {
            if ($category->getParentCategoryId() == $categoryId) {
                $data[] = [
                    'value' => $category->getId(),
                    'label' => $category->getName(),
                ];
            }
        }
        return $data;
    }
}