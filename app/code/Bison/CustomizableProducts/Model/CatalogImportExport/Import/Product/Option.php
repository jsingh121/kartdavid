<?php

namespace Bison\CustomizableProducts\Model\CatalogImportExport\Import\Product;


class Option extends \Magento\CatalogImportExport\Model\Import\Product\Option
{
    const COLUMN_LAYER_ID = '_custom_option_layer_id';
    const COLUMN_COLOR_NAME = 'color_name';

    /**
     * @var string
     */
    private $columnMaxCharacters = '_custom_option_max_characters';

    /**
     * @param string $name
     * @param array $optionRow
     * @return array
     */
    private function processOptionRow($name, $optionRow)
    {
        $result = [
            self::COLUMN_TYPE => $name ? $optionRow['type'] : '',
            self::COLUMN_ROW_TITLE => '',
            self::COLUMN_ROW_PRICE => ''
        ];
        if (isset($optionRow['_custom_option_store'])) {
            $result[self::COLUMN_STORE] = $optionRow['_custom_option_store'];
        }
        if (isset($optionRow['required'])) {
            $result[self::COLUMN_IS_REQUIRED] = $optionRow['required'];
        }
        if (isset($optionRow['sku'])) {
            $result[self::COLUMN_ROW_SKU] = $optionRow['sku'];
            $result[self::COLUMN_PREFIX . 'sku'] = $optionRow['sku'];
        }
        if (isset($optionRow['option_title'])) {
            $result[self::COLUMN_ROW_TITLE] = $optionRow['option_title'];
        }
        if (isset($optionRow['layer_id'])) {
            $result[self::COLUMN_LAYER_ID] = $optionRow['layer_id'];
        }
        if (isset($optionRow['color_name'])) {
            $result[self::COLUMN_COLOR_NAME] = $optionRow['color_name'];
        }

        if (isset($optionRow['price'])) {
            $percent_suffix = '';
            if (isset($optionRow['price_type']) && $optionRow['price_type'] == 'percent') {
                $percent_suffix = '%';
            }
            $result[self::COLUMN_ROW_PRICE] = $optionRow['price'] . $percent_suffix;
        }

        $result[self::COLUMN_PREFIX . 'price'] = $result[self::COLUMN_ROW_PRICE];

        if (isset($optionRow['max_characters'])) {
            $result[$this->columnMaxCharacters] = $optionRow['max_characters'];
        }

        $result = $this->addFileOptions($result, $optionRow);

        return $result;
    }


    /**
     * Retrieve specific type data
     *
     * @param array $rowData
     * @param int $optionTypeId
     * @param bool $defaultStore
     * @return array|false
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _getSpecificTypeData(array $rowData, $optionTypeId, $defaultStore = true)
    {
        if (!empty($rowData[self::COLUMN_ROW_TITLE]) && $defaultStore && empty($rowData[self::COLUMN_STORE])) {
            $valueData = [
                'option_type_id' => $optionTypeId,
                'sort_order' => empty($rowData[self::COLUMN_ROW_SORT]) ? 0 : abs($rowData[self::COLUMN_ROW_SORT]),
                'sku' => !empty($rowData[self::COLUMN_ROW_SKU]) ? $rowData[self::COLUMN_ROW_SKU] : '',
                'color_name' => !empty($rowData[self::COLUMN_COLOR_NAME]) ? $rowData[self::COLUMN_COLOR_NAME] : '',
            ];

            $priceData = false;
            if (!empty($rowData[self::COLUMN_ROW_PRICE])) {
                $priceData = [
                    'price' => (double)rtrim($rowData[self::COLUMN_ROW_PRICE], '%'),
                    'price_type' => 'fixed',
                ];
                if ('%' == substr($rowData[self::COLUMN_ROW_PRICE], -1)) {
                    $priceData['price_type'] = 'percent';
                }
            }
            return ['value' => $valueData, 'title' => $rowData[self::COLUMN_ROW_TITLE], 'price' => $priceData];
        } elseif (!empty($rowData[self::COLUMN_ROW_TITLE]) && !$defaultStore && !empty($rowData[self::COLUMN_STORE])) {
            return ['title' => $rowData[self::COLUMN_ROW_TITLE]];
        }
        return false;
    }

    /**
     * Get multiRow format from one line data.
     *
     * @param array $rowData
     * @return array
     */
    protected function _getMultiRowFormat($rowData)
    {
        // Parse custom options.
        $rowData = $this->_parseCustomOptions($rowData);
        $multiRow = [];
        if (empty($rowData['custom_options'])) {
            return $multiRow;
        }

        $i = 0;
        foreach ($rowData['custom_options'] as $name => $customOption) {
            $i++;
            foreach ($customOption as $rowOrder => $optionRow) {
                $row = array_merge(
                    [
                        self::COLUMN_STORE => '',
                        self::COLUMN_TITLE => $name,
                        self::COLUMN_SORT_ORDER => $i,
                        self::COLUMN_ROW_SORT => $rowOrder
                    ],
                    $this->processOptionRow($name, $optionRow)
                );
                $name = '';
                $multiRow[] = $row;
            }
        }

        return $multiRow;
    }

    /**
     * Add file options
     *
     * @param array $result
     * @param array $optionRow
     * @return array
     */
    private function addFileOptions($result, $optionRow)
    {
        foreach (['file_extension', 'image_size_x', 'image_size_y'] as $fileOptionKey) {
            if (!isset($optionRow[$fileOptionKey])) {
                continue;
            }

            $result[self::COLUMN_PREFIX . $fileOptionKey] = $optionRow[$fileOptionKey];
        }

        return $result;
    }

    /**
     * Retrieve option data
     *
     * @param array $rowData
     * @param int $productId
     * @param int $optionId
     * @param string $type
     * @return array
     */
    protected function _getOptionData(array $rowData, $productId, $optionId, $type)
    {
        $optionData = [
            'option_id' => $optionId,
            'sku' => '',
            'max_characters' => 0,
            'file_extension' => null,
            'image_size_x' => 0,
            'image_size_y' => 0,
            'product_id' => $productId,
            'type' => $type,
            'is_require' => empty($rowData[self::COLUMN_IS_REQUIRED]) ? 0 : 1,
            'sort_order' => empty($rowData[self::COLUMN_SORT_ORDER]) ? 0 : abs($rowData[self::COLUMN_SORT_ORDER]),
            'layer_id' => $rowData[self::COLUMN_LAYER_ID] ?? '',

        ];

        if (!$this->_isRowHasSpecificType($type)) {
            // simple option may have optional params
            foreach ($this->_specificTypes[$type] as $paramSuffix) {
                if (isset($rowData[self::COLUMN_PREFIX . $paramSuffix])) {
                    $data = $rowData[self::COLUMN_PREFIX . $paramSuffix];

                    if (array_key_exists($paramSuffix, $optionData)) {
                        $optionData[$paramSuffix] = $data;
                    }
                }
            }
        }
        return $optionData;
    }
}