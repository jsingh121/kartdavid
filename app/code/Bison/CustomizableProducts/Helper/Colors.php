<?php

namespace Bison\CustomizableProducts\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Registry;
use Magento\Catalog\Model\Product\Option;

/**
 * Class Colors
 * @package Bison\CustomizableProducts\Helper
 */
class Colors extends AbstractHelper
{
    /** @var Registry */
    protected $registry;

    /** @var Option */
    protected $option;

    /**
     * Colors constructor.
     * @param Context $context
     * @param Registry $registry
     * @param Option $option
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Option $option
    )
    {
        parent::__construct($context);
        $this->registry = $registry;
        $this->option = $option;
    }

    /**
     * Get option by layer id
     * @param string $layerId
     *
     * @return Option\Value[]|
     */
    public function getColorOptionValuesByLayerId(string $layerId)
    {
        $product = $this->registry->registry('current_product');
        if (!$product) {
            return;
        }

        $options = $this->option->getProductOptions($product);
        foreach ($options as $option) {
            if ($option->getData('layer_id') === $layerId) {
                return $option->getValues();
            }
        }
    }

}