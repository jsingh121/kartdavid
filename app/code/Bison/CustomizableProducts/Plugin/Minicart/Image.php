<?php

namespace Bison\CustomizableProducts\Plugin\Minicart;

use Magento\Store\Model\StoreManagerInterface;

class Image
{
    public $_storeManager;

    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
    }

    public function aroundGetItemData($subject, $proceed, $item)
    {
        $mediaUrl = $this ->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $result = $proceed($item);
        if ($item->getData('generated_svg')) {
            $result['product_image']['src'] = $mediaUrl . $item->getData('generated_svg');
        }
        return $result;
    }
}
