<?php

namespace Bison\CustomizableProducts\Model\Source;

use Bison\CustomizableProducts\Model\ResourceModel\LogoCategory\CollectionFactory;

class LogoCategory implements \Magento\Framework\Data\OptionSourceInterface
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
        $data = [
            [
                'value' => 0,
                'label' => 'Root Category'
            ]
        ];

        $collection = $this->logoCategoryCollectionFactory->create();

        foreach ($collection as $category) {
            if ($category->getParentCategoryId() == 0) {
                $data[] = [
                    'value' => $category->getId(),
                    'label' => $category->getName()
                ];

            }
        }
        return $data;
    }
}