<?php

namespace Bison\CustomizableProducts\Plugin;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Field;
use Bison\CustomizableProducts\Block\Customizer\Inputs;

class OptionAttribute {

    const LAYER_ID_CODE = 'layer_id';

    /**
     * Added layer id field to catalog product option form
     * @param CustomOptions $subject
     * @param $meta
     * @return mixed
     */
    public function afterModifyMeta(
        CustomOptions $subject,
        $meta
    ) {
        $meta['custom_options']['children']['options']['children']['record']['children']['container_option']['children']
        ['container_common']['children'][self::LAYER_ID_CODE] =
            [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label'         => __( 'Layer ID' ),
                            'component' => 'Magento_Catalog/js/custom-options-type',
                            'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                            'componentType' => Field::NAME,
                            'formElement'   => Select::NAME,
                            'options'       => $this->getOptions(),
                            'selectType' => 'optgroup',
                            'disableLabel' => true,
                            'multiple' => false,
                            'selectedPlaceholders' => [
                                'defaultPlaceholder' => __('--Select Layer--'),
                            ],
                            'dataScope'     => self::LAYER_ID_CODE,
                            'dataType'      => Text::NAME,
                            'sortOrder'     => 200,
                            'validation'    => [
                                'required-entry' => false
                            ],
                        ],
                    ],
                ]
            ];

        return $meta;
    }

    /**
     * Get select layer_id options
     * @return array
     */
    private function getOptions() : array
    {
         return [
            [
                'value' => 0,
                'label' => '---'
            ],
            [
                'value' => Inputs::DRIVER_COLORS_LAYER_ID,
                'label' => $this->toLabel(Inputs::DRIVER_COLORS_LAYER_ID)
            ],
             [
                 'value' => Inputs::LAYER_1_COLORS_LAYER_ID,
                 'label' => $this->toLabel(Inputs::LAYER_1_COLORS_LAYER_ID)
             ],
             [
                 'value' => Inputs::LAYER_2_COLORS_LAYER_ID,
                 'label' => $this->toLabel(Inputs::LAYER_2_COLORS_LAYER_ID)
             ],
             [
                 'value' => Inputs::LAYER_3_COLORS_LAYER_ID,
                 'label' => $this->toLabel(Inputs::LAYER_3_COLORS_LAYER_ID)
             ],
             [
                 'value' => Inputs::NUMBER_COLORS_LAYER_ID,
                 'label' => $this->toLabel(Inputs::NUMBER_COLORS_LAYER_ID)
             ],
             [
                 'value' => Inputs::NUMBER_BACKGROUND_COLORS_LAYER_ID,
                 'label' => $this->toLabel(Inputs::NUMBER_BACKGROUND_COLORS_LAYER_ID)
             ]
        ];
    }

    /**
     * @param string $code
     * @return string
     */
    private function toLabel(string $code) : string
    {
        return str_replace('_', ' ', ucwords($code));
    }
}