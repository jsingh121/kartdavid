<?php

namespace Bison\SvgFonts\Helper;

use Bison\SvgFonts\Model\Font\Options;
use Bison\SvgFonts\Model\ResourceModel\Font\CollectionFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Fonts extends AbstractHelper
{
    /**
     * Collection factory
     *
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Fonts constructor.
     *
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory
    )
    {
        parent::__construct($context);

        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Returns an array with fonts
     *
     * @param null $fontType
     * @return array
     */
    public function getFonts($fontType = null)
    {
        $collection = $this->collectionFactory->create();

        if ($fontType) {
            $collection->addFieldToFilter('font_type', [
                ['eq' => [$fontType]],
                ['eq' => [Options::NUMBER_NAME_CODE]]
            ]);
        }

        $fonts = [];

        foreach ($collection as $font) {
            $fonts[] = [
                'name' => $this->convertNameToCode($font->getFontName()),
                'url' => $font->getFontFileUrl(),
                'base64_url' => $this->convertToBase64($font->getFontFileUrl())
            ];
        }

        return $fonts;
    }

    /**
     * Converts name to code
     *
     * @param string $name
     *
     * @return string
     */
    protected function convertNameToCode($name)
    {
        return str_replace(' ', '-', $name);
    }

    /**
     * Convert fonts to base64 scheme
     *
     * @param $font
     * @return string
     */
    private function convertToBase64($font)
    {
        $base64font = base64_encode(file_get_contents($font));
        return "data:application/octet-stream;base64,$base64font";
    }
}