<?php

namespace Bison\CustomizableProducts\ViewModel;

use Bison\CustomizableProducts\Model\ConfigProvider;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Svg implements ArgumentInterface
{
    /**
     * Config Provider
     *
     * @var ConfigProvider
     */
    protected $configProvider;

    /**
     * Svg view model constructor
     *
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    /**
     * Returns API ID
     *
     * @return string
     */
    public function getApiId()
    {
        return $this->configProvider->getApiId();
    }
}