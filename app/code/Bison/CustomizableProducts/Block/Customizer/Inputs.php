<?php

namespace Bison\CustomizableProducts\Block\Customizer;

use Bison\CustomizableProducts\Helper\Colors;
use Bison\CustomizableProducts\Helper\PredefinedLogo;
use Bison\SvgFonts\Helper\Fonts;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Directory\Model\Currency;

class Inputs extends Template
{
    const LAYER_1_COLORS_LAYER_ID = 'layer_1';
    const LAYER_2_COLORS_LAYER_ID = 'layer_2';
    const LAYER_3_COLORS_LAYER_ID = 'layer_3';
    const NUMBER_COLORS_LAYER_ID = 'number';
    const NUMBER_BACKGROUND_COLORS_LAYER_ID = 'number_bkg';
    const DRIVER_COLORS_LAYER_ID = 'driver';

    /**
     * Colors helper
     *
     * @var Colors
     */
    protected $colorsHelper;

    /**
     * Font helper
     *
     * @var Fonts
     */
    protected $fontHelper;

    /**
     * Predefined logo helper
     *
     * @var PredefinedLogo
     */
    protected $predefinedLogo;

    /**
     * Currency model
     *
     * @var Currency
     */
    protected $currency;

    /**
     * Registry
     *
     * @var Registry
     */
    protected $registry;

    /**
     * Inputs constructor.
     *
     * @param Template\Context $context
     * @param Fonts $fontHelper
     * @param Colors $colors
     * @param PredefinedLogo $predefinedLogo
     * @param Currency $currency
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Colors $colors,
        Fonts $fontHelper,
        PredefinedLogo $predefinedLogo,
        Currency $currency,
        Registry $registry,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );

        $this->registry = $registry;
        $this->colorsHelper = $colors;
        $this->fontHelper = $fontHelper;
        $this->predefinedLogo = $predefinedLogo;
        $this->currency = $currency;

    }

    /**
     * Returns available colors for driver number
     *
     * @return \Magento\Catalog\Model\Product\Option\Value[]
     */
    public function getNumberColors()
    {
        return $this->colorsHelper->getColorOptionValuesByLayerId(self::NUMBER_COLORS_LAYER_ID);
    }

    /**
     * Returns available colors for driver number background
     *
     * @return \Magento\Catalog\Model\Product\Option\Value[]
     */
    public function getNumberBackgroundColors()
    {
        return $this->colorsHelper->getColorOptionValuesByLayerId(self::NUMBER_BACKGROUND_COLORS_LAYER_ID);
    }

    /**
     * Returns available colors for layers
     *
     * @param int $layerNumber
     *
     * @return \Magento\Catalog\Model\Product\Option\Value[]
     */
    public function getLayerColors($layerNumber = 1)
    {
        switch ($layerNumber) {
            case 1:
                return $this->colorsHelper->getColorOptionValuesByLayerId(self::LAYER_1_COLORS_LAYER_ID);
            case 2:
                return $this->colorsHelper->getColorOptionValuesByLayerId(self::LAYER_2_COLORS_LAYER_ID);
            case 3:
                return $this->colorsHelper->getColorOptionValuesByLayerId(self::LAYER_3_COLORS_LAYER_ID);

        }
        return [];
    }

    /**
     * Returns available colors for driver name
     *
     * @return \Magento\Catalog\Model\Product\Option\Value[]
     */
    public function getDriverColors()
    {
        return $this->colorsHelper->getColorOptionValuesByLayerId(self::DRIVER_COLORS_LAYER_ID);
    }

    /**
     * Returns an array of available fonts
     *
     * @param null $fontType
     * @return array
     */
    public function getFonts($fontType = null)
    {
        return $this->fontHelper->getFonts($fontType);
    }

    public function getProductLogosCategory()
    {
        $logosData = $this->predefinedLogo->getAvailableLogos();

        $logos = [];
        foreach ($logosData as $logoData) {
            $logos = array_merge($logos, $logoData['logos']);
        }

        usort($logos, function ($a, $b){
           return $a['name'] > $b['name'];
        });

        return $logos;
    }

    /**
     * Returns symbol for current locale and currency code
     *
     * @return string
     */
    public function getCurrentCurrencySymbol()
    {
        return $this->currency->getCurrencySymbol();
    }

    public function getMaxNameLength()
    {
        $product = $this->registry->registry('current_product');

        return $product->getData('name_maximum_length');
    }

}
