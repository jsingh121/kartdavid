<?php

namespace Bison\CustomizableProducts\Plugin\Quote;

use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Quote\Model\Quote\Item\ToOrderItem;
use Magento\Quote\Model\Quote\Item\AbstractItem;

class GeneratedSvgToOrderItem
{
    /**
     * Copies generated svg from quote item to order item
     *
     * @param ToOrderItem $subject
     * @param OrderItemInterface $orderItem
     * @param AbstractItem $item
     * @param array $additional
     * @return OrderItemInterface
     */
    public function afterConvert(
        ToOrderItem $subject,
        OrderItemInterface $orderItem,
        AbstractItem $item,
        $additional = []
    ) {
        $orderItem->setData('generated_svg', $item->getData('generated_svg'));;
        return $orderItem;
    }
}
