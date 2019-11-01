<?php

namespace Bison\CustomizableProducts\Model\Category;

use Bison\CustomizableProducts\Model\LogoCategory;
use Bison\CustomizableProducts\Model\ResourceModel\LogoCategory\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * Font collection factory
     *
     * @var CollectionFactory
     */
    protected $fontCollectionFactory;

    protected $loadedData = null;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $fontCollectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $fontCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->fontCollectionFactory = $fontCollectionFactory;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if ($this->loadedData === null) {
            $this->loadData();
        }

        return $this->loadedData;
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        return null;
    }

    /**
     * Loads data
     */
    protected function loadData()
    {
        /** @var LogoCategory[] $collection */
        $collection = $this->fontCollectionFactory->create();

        $this->loadedData = [];
        foreach($collection as $category) {
            $this->loadedData[$category->getId()] = [
                'category_id' => $category->getId(),
                'name' => $category->getName(),
                'parent_category_id' => $category->getParentCategoryId()
            ];
        }
    }

}
