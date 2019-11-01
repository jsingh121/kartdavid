<?php

namespace Bison\CustomizableProducts\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Logo extends AbstractHelper
{
    /**
     * XML parser
     *
     * @var \Magento\Framework\Xml\Parser
     */
    protected $xmlParser;

    /**
     * Logo constructor.
     *
     * @param Context $context
     * @param \Magento\Framework\Xml\Parser $xmlParser
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Xml\Parser $xmlParser
    ) {
        parent::__construct($context);

        $this->xmlParser = $xmlParser;
    }

    /**
     * Returns dimension of logo
     *
     * @param string $filePath
     *
     * @return array
     */
    public function getLogoDimension($filePath)
    {
        $fileExtension = $this->getFileExtension($filePath);

        if ($fileExtension === 'svg') {
            return $this->getSvgDimension($filePath);
        }

        return $this->getImageDimension($filePath);
    }

    /**
     * Returns svg image dimension
     *
     * @param string $filePath
     *
     * @return array
     */
    protected function getSvgDimension($filePath)
    {
        $svg = $this->xmlParser->load($filePath);
        $xml = $svg->xmlToArray();

        if (!isset($xml['svg']['_attribute']['width'])) {
            $xml['svg']['_attribute']['width'] = explode(' ', $xml['svg']['_attribute']['viewBox'])[2];
        }

        if (!isset($xml['svg']['_attribute']['height'])) {
            $xml['svg']['_attribute']['height'] = explode(' ', $xml['svg']['_attribute']['viewBox'])[3];
        }

        return [(int) $xml['svg']['_attribute']['width'], (int) $xml['svg']['_attribute']['height']];
    }

    /**
     * Returns product image dimension
     *
     * @param string $filePath
     *
     * @return array
     */
    protected  function getImageDimension($filePath)
    {
        list($width, $height) = getimagesize($filePath);

        return [$width, $height];
    }

    /**
     * Returns file extension
     *
     * @param string $filePath
     *
     * @return string
     */
    protected function getFileExtension($filePath)
    {
        return substr($filePath, strrpos($filePath, '.')+1);
    }

}
