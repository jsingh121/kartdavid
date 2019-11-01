<?php

namespace Bison\CustomizableProducts\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Svg
 * @package Bison\CustomizableProducts\Helper
 */
class Svg extends AbstractHelper
{
    const SCRIPT_PATTERN = "#<script (.*)>(.*)</script>#si";
    const RESIZE_IMAGE_PATTERN = '/<image([^>]*)class="pointer(.*)"(.*)><\/image>/siU';

    /**
     * Check if svg (string) contains script tag.
     * @param string $svg
     * @return bool
     */
    public function hasScriptTags(string $svg) : bool
    {
        if (preg_match(self::SCRIPT_PATTERN, $svg)) {
            return true;
        }

        return false;
    }

    /**
     * Remove script tags from svg file.
     * @param string $svg
     * @param string $filePath
     */
    public function removeScriptTags(string $svg, string $filePath)
    {
        $svg = preg_replace(self::SCRIPT_PATTERN, '', $svg);
        file_put_contents($filePath, $svg);
    }

    /**
     * Clears svg file before downloading it
     *
     * @param string $svg
     *
     * @return string
     */
    public function clearForDownload(string $svg)
    {
        $svg = preg_replace(self::SCRIPT_PATTERN, '', $svg);
        $svg = preg_replace(self::RESIZE_IMAGE_PATTERN, '', $svg);

        return $svg;
    }
}