<?php

namespace Bison\CustomizableProducts\Model\Source;

use Bison\CustomizableProducts\Model\ResourceModel\LogoCategory\CollectionFactory;

class LogoAttribute extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
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
    public function getAllOptions()
    {
        $data = [
            [
                'label' => __('Please select.'),
                'value' => 0
            ],
        ];

        $collection = $this->logoCategoryCollectionFactory->create();

        foreach ($collection as $category) {
            if ($category->getParentCategoryId() == 0) {
                $data[] = [
                    'label' => $category->getName(),
                    'value' => $category->getId(),

                ];
            }
        }

        return $data;
    }
}