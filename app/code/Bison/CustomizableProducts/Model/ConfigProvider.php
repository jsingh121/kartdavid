<?php

namespace Bison\CustomizableProducts\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider
{
    const API_ID_PATH = 'customizer/background_removal/api_id';

    const API_KEY_PATH = 'customizer/background_removal/api_key';

    const API_MODE_PATH = 'customizer/background_removal/test_mode';

    /**
     * Scope config
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * ConfigProvider constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Returns ClippingMagic API ID
     *
     * @return string
     */
    public function getApiId()
    {
        return $this->scopeConfig->getValue(self::API_ID_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Returns ClippingMagic API Key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->scopeConfig->getValue(self::API_KEY_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Returns ClippingMagic API Key
     *
     * @return string
     */
    public function isTestMode()
    {
        return $this->scopeConfig->getValue(self::API_MODE_PATH, ScopeInterface::SCOPE_STORE) === '0';
    }
}