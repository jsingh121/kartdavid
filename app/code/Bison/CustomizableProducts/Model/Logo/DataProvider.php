<?php

namespace Bison\CustomizableProducts\Model\Logo;

use Bison\CustomizableProducts\Model\ResourceModel\PredefinedLogo\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * Collection factory
     *
     * @var CollectionFactory
     */
    protected $collectionFactory;

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

        $this->collectionFactory = $fontCollectionFactory;
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
        $collection = $this->collectionFactory->create();

        $this->loadedData = [];
        foreach($collection as $logo) {
            $this->loadedData[$logo->getId()] = [
                'logo_id' => $logo->getId(),
                'name' => $logo->getName(),
                'category_id' => $logo->getCategoryId(),
                'logo' => [
                    0 => [
                        'name' => $logo->getFilename(),
                        'url'  => $logo->getLogoUrl(),
                        'path' => $logo->getLogoPath(),
                        'size' => filesize($logo->getLogoPath()),
                    ]
                ]
            ];
        }
    }

}
