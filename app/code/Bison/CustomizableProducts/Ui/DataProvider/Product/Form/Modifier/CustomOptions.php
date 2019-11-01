<?php

namespace Bison\CustomizableProducts\Ui\DataProvider\Product\Form\Modifier;

use Magento\Ui\Component\Container;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Element\Input;

class CustomOptions extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions
{
    const FIELD_IS_FLUORESCENT_NAME = 'is_fluorescent';

    const FIELD_COLOR_NAME = 'color_name';

    /**
     * Get config for grid for "select" types
     * @param int $sortOrder
     * @return array
     */
    protected function getSelectTypeGridConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'addButtonLabel' => __('Add Value'),
                        'componentType' => DynamicRows::NAME,
                        'component' => 'Magento_Ui/js/dynamic-rows/dynamic-rows',
                        'additionalClasses' => 'admin__field-wide',
                        'deleteProperty' => self::FIELD_IS_DELETE,
                        'deleteValue' => '1',
                        'renderDefaultRecord' => false,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'positionProvider' => self::FIELD_SORT_ORDER_NAME,
                                'isTemplate' => true,
                                'is_collection' => true,
                            ],
                        ],
                    ],
                    'children' => [
                        static::FIELD_TITLE_NAME => $this->getTitleFieldConfig(10),
                        static::FIELD_PRICE_NAME => $this->getPriceFieldConfig(20),
                        static::FIELD_PRICE_TYPE_NAME => $this->getPriceTypeFieldConfig(30, ['fit' => true]),
                        static::FIELD_SKU_NAME => $this->getSkuFieldConfig(40),
                        static::FIELD_SORT_ORDER_NAME => $this->getPositionFieldConfig(50),
                        static::FIELD_IS_FLUORESCENT_NAME => $this->getIsFluorescentFieldConfig(55),
                        static::FIELD_COLOR_NAME => $this->getColorNameFieldConfig(54),
                        static::FIELD_IS_DELETE => $this->getIsDeleteFieldConfig(60)
                    ]
                ]
            ]
        ];
    }

    /**
     * Get config for "is fluorescent" fields
     *
     * @param $sortOrder
     * @param array $options
     * @return array
     */
    protected function getIsFluorescentFieldConfig($sortOrder, array $options = [])
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Is Fluorescent'),
                        'componentType' => Field::NAME,
                        'formElement' => Checkbox::NAME,
                        'dataScope' => self::FIELD_IS_FLUORESCENT_NAME,
                        'dataType' => Number::NAME,
                        'sortOrder' => $sortOrder,
                        'value' => '0',
                        'valueMap' => [
                            'true' => '1',
                            'false' => '0'
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get config for "color name" fields
     *
     * @param $sortOrder
     * @param array $options
     * @return array
     */
    protected function getColorNameFieldConfig($sortOrder, array $options = [])
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Color Name'),
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'dataScope' => self::FIELD_COLOR_NAME,
                        'dataType' => Text::NAME,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];
    }
}
