<?php

namespace Bison\CustomizableProducts\Helper;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Filesystem;

/**
 * Bodywork helper
 *
 * @package Bison\CustomizableProducts\Helper
 */
class Bodywork extends AbstractHelper
{
    const PATH_TO_SAVE = 'catalog/product/file/';

    /**
     * Filesystem handle
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Bodywork helper constructor.
     *
     * @param Context $context
     * @param Filesystem $filesystem
     */
    public function __construct(
        Context $context,
        Filesystem $filesystem
    )
    {
        parent::__construct($context);
        $this->filesystem = $filesystem;
    }

    /**
     * Returns product svg bodywork file path
     *
     * @param Product $product
     *
     * @return string
     */
    public function getProductBodyworkPath(Product $product)
    {
        $path = $this->filesystem
            ->getDirectoryRead(DirectoryList::MEDIA)
            ->getAbsolutePath(self::PATH_TO_SAVE);

        return $path.$product->getData('svg_bodywork');
    }

    /**
     * Returns product bodywork
     *
     * @param Product $product
     *
     * @return string
     */
    public function getProductBodywork(Product $product)
    {
        $path = $this->getProductBodyworkPath($product);

        if (file_exists($path) && is_readable($path)) {
            return file_get_contents($path);
        }

        return '';
    }

    /**
     * Returns bodywork content by url
     *
     * @param string $url
     * @return string
     */
    public function getBodyworkByUrl(string $url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return file_get_contents($url);
        }

        return '';
    }

}