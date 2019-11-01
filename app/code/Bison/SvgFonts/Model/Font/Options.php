<?php

namespace Bison\SvgFonts\Model\Font;

/**
 * Class Options
 * @package Bison\SvgFonts\Model\Font
 */
class Options implements \Magento\Framework\Option\ArrayInterface
{
    const NUMBER_NAME_CODE = 'number_name';
    const NUMBER_CODE = 'number';
    const NAME_CODE = 'name';

    /**
     * Returns array with font type options
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::NUMBER_NAME_CODE, 'label' => __('Number & Name')],
            ['value' => self::NUMBER_CODE, 'label' => __('Number')],
            ['value' => self::NAME_CODE, 'label' => __('Name')]
        ];
    }
}