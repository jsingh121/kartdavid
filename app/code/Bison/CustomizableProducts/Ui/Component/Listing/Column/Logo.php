<?php

namespace Bison\CustomizableProducts\Ui\Component\Listing\Column;

use Bison\CustomizableProducts\Model\PredefinedLogoFactory;

class Logo extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Logo factory
     *
     * @var PredefinedLogoFactory
     */
    protected $logoFactory;

    /**
     * constructor
     *
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Bison\CustomizableProducts\Model\PredefinedLogoFactory $logoFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        PredefinedLogoFactory $logoFactory,
        array $components = [],
        array $data = []
    ) {
        $this->logoFactory = $logoFactory;
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
        $logo = $this->logoFactory->create();

        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                $logo->load($item['logo_id']);

                $item[$fieldName . '_src'] = $logo->getLogoUrl();
                $item[$fieldName . '_alt'] = $logo->getFilename();
                $item[$fieldName . '_orig_src'] = $logo->getLogoUrl();
            }
        }

        return $dataSource;
    }
}