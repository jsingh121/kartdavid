<?php

namespace Bison\CustomizableProducts\Model\Cart;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @api
 */
class ImageProvider extends \Magento\Checkout\Model\Cart\ImageProvider
{
    /**
     * {@inheritdoc}
     */
    public function getImages($cartId)
    {
        $itemData = [];

        /** @see code/Magento/Catalog/Helper/Product.php */
        $items = $this->itemRepository->getList($cartId);
        /** @var \Magento\Quote\Model\Quote\Item $cartItem */
        foreach ($items as $cartItem) {
            $allData = $this->itemPool->getItemData($cartItem);
            $itemData[$cartItem->getItemId()] = $allData['product_image'];
            if ($cartItem->getData('generated_svg')) {
                $itemData[$cartItem->getItemId()]['generated_svg'] = $cartItem->getData('generated_svg');
            }
        }
        return $itemData;
    }
}
