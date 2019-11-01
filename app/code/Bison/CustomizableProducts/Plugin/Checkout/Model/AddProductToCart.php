<?php
namespace Bison\CustomizableProducts\Plugin\Checkout\Model;

use \Magento\Catalog\Model\Product;
use \Magento\Checkout\Model\Cart;

class AddProductToCart
{
    /**
     * Removes generated svg from buy request info
     * @param Cart $cart
     * @param Product $product
     * @param array $productInfo
     *
     * @return array
     */
    public function beforeAddProduct(
        Cart $cart,
        Product $product,
        array $productInfo
    ) {
        unset($productInfo['generated_svg']);

        return [$product, $productInfo];
    }
}
