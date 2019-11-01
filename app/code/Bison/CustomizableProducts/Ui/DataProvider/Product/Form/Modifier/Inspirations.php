<?php

namespace Bison\CustomizableProducts\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\ActionDelete;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\DynamicRows;
use Bison\CustomizableProducts\Model\InspirationFactory;

/**
 * Class Inspirations
 * @package Bison\CustomizableProducts\Ui\DataProvider\Product\Form\Modifier
 */
class Inspirations extends AbstractModifier
{
    /** Group values */
    const GROUP_INSPIRATION_NAME = 'custom_inspirations';
    const GROUP_INSPIRATION_SCOPE = 'data.product';
    const GROUP_INSPIRATION_PREVIOUS_NAME = 'search-engine-optimization';
    const GROUP_INSPIRATION_DEFAULT_SORT_ORDER = 50;

    /** Button values */
    const BUTTON_ADD = 'button_add';

    /** Container values */
    const CONTAINER_HEADER_NAME = 'container_header';
    const CONTAINER_OPTION = 'container_inspiration';
    const CONTAINER_COMMON_NAME = 'container_common';
    const CONTAINER_TYPE_STATIC_NAME = 'container_type_static';

    /** Grid values */
    const GRID_INSPIRATION_NAME = 'inspiration';
    const GRID_TYPE_SELECT_NAME = 'values';

    /** Field values */
    const FIELD_ENABLE = 'affect_product_inspiration';
    const FIELD_INSPIRATION_ID = 'entity_id';
    const FIELD_NAME = 'name';
    const FIELD_INSPIRATION_NAME = 'inspiration';
    const FIELD_SORT_ORDER_NAME = 'position';
    const FIELD_IMAGE_NAME = 'image';
    const FIELD_IS_DELETE = 'is_delete';
    const STATUS_OLD_CODE = 'old';

    /** Import options values */
    const INSPIRATION_LISTING = 'product_custom_inspiration_listing';

    /** @var LocatorInterface */
    protected $locator;

    /** @var StoreManagerInterface */
    protected $storeManager;

    /** @var UrlInterface */
    protected $urlBuilder;

    /** @var ArrayManager */
    protected $arrayManager;

    /** @var array */
    protected $meta = [];

    /** @var InspirationFactory|DataProvider */
    protected $inspirations;

    /**
     * Inspirations constructor.
     * @param LocatorInterface $locator
     * @param StoreManagerInterface $storeManager
     * @param UrlInterface $urlBuilder
     * @param ArrayManager $arrayManager
     * @param InspirationFactory $inspiration
     */
    public function __construct(
        LocatorInterface $locator,
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder,
        ArrayManager $arrayManager,
        InspirationFactory $inspiration
    )
    {
        $this->locator = $locator;
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
        $this->arrayManager = $arrayManager;
        $this->inspirations = $inspiration;
    }

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        $inspirations = [];
        $productId = $this->locator->getProduct()->getId();
        $customInspirations = $this->getInspirationsData() ?: [];

        foreach ($customInspirations as $inspiration) {
            $descData = $this->addImageData($inspiration);
            $inspirations[] = $descData;
        }

        $replaced = array_replace_recursive(
            $data,
            [
                $productId => [
                    self::DATA_SOURCE_DEFAULT => [
                        self::FIELD_ENABLE => 1,
                        self::GRID_INSPIRATION_NAME => $inspirations
                    ]
                ]
            ]
        );

        return $replaced;
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;
        $this->createCustomInspirationPanel();
        return $this->meta;
    }

    /**
     * Create "Inspiration" panel
     * @return $this
     */
    protected function createCustomInspirationPanel()
    {
        $this->meta = array_replace_recursive(
            $this->meta,
            [
                self::GROUP_INSPIRATION_NAME => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Inspirations'),
                                'componentType' => Fieldset::NAME,
                                'dataScope' => self::GROUP_INSPIRATION_SCOPE,
                                'collapsible' => true,
                                'sortOrder' => $this->getNextGroupSortOrder(
                                    $this->meta,
                                    self::GROUP_INSPIRATION_PREVIOUS_NAME,
                                    self::GROUP_INSPIRATION_DEFAULT_SORT_ORDER
                                ),
                            ],
                        ],
                    ],
                    'children' => [
                        self::CONTAINER_HEADER_NAME => $this->getHeaderContainerConfig(10),
                        self::FIELD_ENABLE => $this->getEnableFieldConfig(20),
                        self::GRID_INSPIRATION_NAME => $this->getOptionsGridConfig(30)
                    ]
                ]
            ]
        );

        return $this;
    }

    /**
     * Get config for header container
     * @param $sortOrder
     * @return array
     */
    protected function getHeaderContainerConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => null,
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'template' => 'ui/form/components/complex',
                        'sortOrder' => $sortOrder
                    ],
                ],
            ],
            'children' => [
                self::BUTTON_ADD => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'title' => __('Add Inspiration'),
                                'formElement' => Container::NAME,
                                'componentType' => Container::NAME,
                                'component' => 'Magento_Ui/js/form/components/button',
                                'sortOrder' => 20,
                                'actions' => [
                                    [
                                        'targetName' => 'ns = ${ $.ns }, index = ' . self::GRID_INSPIRATION_NAME,
                                        'actionName' => 'processingAddChild',
                                    ]
                                ]
                            ]
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get config for the whole grid
     * @param int $sortOrder
     * @return array
     */
    protected function getOptionsGridConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => DynamicRows::NAME,
                        'template' => 'ui/dynamic-rows/templates/collapsible',
                        'additionalClasses' => 'admin__field-wide',
                        'deleteProperty' => self::FIELD_IS_DELETE,
                        'deleteValue' => '1',
                        'addButton' => false,
                        'renderDefaultRecord' => false,
                        'columnsHeader' => false,
                        'collapsibleHeader' => true,
                        'sortOrder' => $sortOrder,
                    ]
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'headerLabel' => __('Inspirations'),
                                'componentType' => Container::NAME,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'positionProvider' => self::CONTAINER_OPTION . '.' . self::FIELD_SORT_ORDER_NAME,
                                'isTemplate' => true,
                                'is_collection' => true,
                            ],
                        ],
                    ],
                    'children' => [
                        self::CONTAINER_OPTION => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Fieldset::NAME,
                                        'label' => null,
                                        'sortOrder' => 10,
                                        'opened' => true,
                                    ],
                                ],
                            ],
                            'children' => [
                                self::FIELD_SORT_ORDER_NAME => $this->getPositionFieldConfig(40),
                                self::CONTAINER_COMMON_NAME => $this->getCommonContainerConfig(10)
                            ]
                        ],
                    ]
                ]
            ]
        ];
    }

    /**
     * Get config for hidden field responsible for enabling custom inspirations processing
     * @param int $sortOrder
     * @return array
     */
    protected function getEnableFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => Field::NAME,
                        'componentType' => Input::NAME,
                        'dataScope' => self::FIELD_ENABLE,
                        'dataType' => Number::NAME,
                        'visible' => false,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];
    }

    /**
     * Get config for container with common fields for any type
     * @param int $sortOrder
     * @return array
     */
    protected function getCommonContainerConfig($sortOrder)
    {
        $commonContainer = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Container::NAME,
                        'formElement' => Container::NAME,
                        'component' => 'Magento_Ui/js/form/components/group',
                        'breakLine' => false,
                        'showLabel' => false,
                        'additionalClasses' => 'admin__field-group-columns admin__control-group-equal',
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
            'children' => [
                self::FIELD_INSPIRATION_ID => $this->getOptionIdFieldConfig(10),
                self::FIELD_IMAGE_NAME => $this->getImageFieldConfig(40)
            ]
        ];

        return $commonContainer;
    }

    /**
     * Get config for hidden id field
     * @param int $sortOrder
     * @return array
     */
    protected function getOptionIdFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => Input::NAME,
                        'componentType' => Field::NAME,
                        'dataScope' => self::FIELD_INSPIRATION_ID,
                        'sortOrder' => $sortOrder,
                        'visible' => false,
                    ],
                ],
            ],
        ];
    }

    /**
     * @param $sortOrder
     * @return array
     */
    protected function getImageFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Image'),
                        'formElement' => 'fileUploader',
                        'componentType' => 'fileUploader',
                        'component' => 'Magento_Ui/js/form/element/file-uploader',
                        'elementTmpl' => 'Magento_Downloadable/components/file-uploader',
                        'fileInputName' => self::FIELD_IMAGE_NAME,
                        'uploaderConfig' => [
                            'url' => $this->urlBuilder->addSessionParam()->getUrl(
                                'bison/inspirations/upload',
                                ['id' => $this->locator->getProduct()->getId(), '_secure' => true]
                            ),
                        ],
                        'allowedExtensions' => 'svg',
                        'dataScope' => 'file',
                        'validation' => [
                            'required-entry' => true,
                        ],
                        'notice' => __('Allowed file types: svg'),
                        'sortOrder' => $sortOrder
                    ],
                ],
            ],
        ];
    }

    /**
     * Get config for hidden field used for sorting
     * @param int $sortOrder
     * @return array
     */
    protected function getPositionFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'dataScope' => self::FIELD_SORT_ORDER_NAME,
                        'dataType' => Number::NAME,
                        'visible' => false,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];
    }

    /**
     * Get config for hidden field used for removing rows
     * @param int $sortOrder
     * @return array
     */
    protected function getIsDeleteFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => ActionDelete::NAME,
                        'fit' => true,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];
    }

    /**
     * @param $descData
     * @return mixed
     */
    private function addImageData(\Bison\CustomizableProducts\Model\Inspiration $inspiration)
    {
        $descData = [];
        if (!empty($inspiration->getData())) {
            $descData['file'][0] = [
                'id' => $inspiration->getId(),
                'file' => $inspiration->getName(),
                'name' => $inspiration->getName(),
                'status' => self::STATUS_OLD_CODE,
                'url' => $inspiration->getInspirationFileUrl(),
                'path' => $inspiration->getInspirationFilePath(),
                'size' => filesize($inspiration->getInspirationFilePath()),
            ];
        }

        return $descData;
    }

    /**
     * Get inspirations collection
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    private function getInspirationsData()
    {
        $inspiration = $this->inspirations->create();
        return $inspiration->getCollection();
    }
}
