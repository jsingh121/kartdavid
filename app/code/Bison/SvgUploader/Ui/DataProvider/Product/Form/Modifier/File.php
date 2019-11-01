<?php

namespace Bison\SvgUploader\Ui\DataProvider\Product\Form\Modifier;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;

class File extends AbstractModifier
{
    const FIELD_CODE = 'svg_bodywork';

    /** @var ArrayManager */
    protected $arrayManager;

    /**
     * File constructor.
     * @param ArrayManager $arrayManager
     */
    public function __construct(ArrayManager $arrayManager)
    {
        $this->arrayManager = $arrayManager;
    }

    public function modifyMeta(array $meta)
    {
        $elementPath = $this->arrayManager->findPath(self::FIELD_CODE, $meta, null, 'children');
        $containerPath = $this->arrayManager->findPath(static::CONTAINER_PREFIX . self::FIELD_CODE, $meta, null, 'children');

        if (!$elementPath) {
            return $meta;
        }

        $meta = $this->arrayManager->merge(
            $containerPath,
            $meta,
            [
                'children' => [
                    self::FIELD_CODE => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'elementTmpl' => 'Bison_SvgUploader/elements/file',
                                ],
                            ],
                        ],
                    ]
                ]
            ]
        );

        return $meta;
    }

    public function modifyData(array $data)
    {
        return $data;
    }
}