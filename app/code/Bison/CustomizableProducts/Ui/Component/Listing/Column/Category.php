<?php

namespace Bison\CustomizableProducts\Ui\Component\Listing\Column;

use Bison\CustomizableProducts\Model\LogoCategoryFactory;

class Category extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Logo category factory
     *
     * @var LogoCategoryFactory
     */
    protected $logoCategoryFactory;

    /**
     * constructor
     *
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Bison\CustomizableProducts\Model\LogoCategoryFactory $logoCategoryFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        LogoCategoryFactory $logoCategoryFactory,
        array $components = [],
        array $data = []
    ) {
        $this->logoCategoryFactory = $logoCategoryFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }


    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $category = $this->logoCategoryFactory->create();

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $category->load($item['category_id']);
                $item['category_id'] = $category->getName();
            }
        }

        return $dataSource;
    }
}