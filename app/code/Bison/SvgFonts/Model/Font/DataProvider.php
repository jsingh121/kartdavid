<?php
namespace Bison\SvgFonts\Model\Font;

use Bison\SvgFonts\Model\ResourceModel\Font\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * Font collection factory
     *
     * @var CollectionFactory
     */
    protected $fontCollectionFactory;

    protected $loadedData = null;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $fontCollectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $fontCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->fontCollectionFactory = $fontCollectionFactory;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if ($this->loadedData === null) {
            $this->loadData();
        }

        return $this->loadedData;
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        return null;
    }

    /**
     * Loads data
     */
    protected function loadData()
    {
        $collection = $this->fontCollectionFactory->create();

        $this->loadedData = [];
        foreach($collection as $font) {
            $this->loadedData[$font->getId()] = [
                'id' => $font->getId(),
                'font_name' => $font->getFontName(),
                'font_type' => $font->getFontType(),
                'font_file' => [
                    0 => [
                        'name' => $font->getFontFileName(),
                        'url'  => $font->getFontFileUrl(),
                        'path' => $font->getFontFilePath(),
                        'size' => filesize($font->getFontFilePath()),
                    ]
                ]
            ];
        }
    }

}
