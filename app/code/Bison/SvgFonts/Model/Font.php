<?php

namespace Bison\SvgFonts\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Framework\Model\AbstractModel;

class Font extends AbstractModel
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Directory list
     *
     * @var DirectoryList
     */
    protected $directoryList;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        DirectoryList $directoryList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->storeManager = $storeManager;
        $this->directoryList = $directoryList;
    }

    /**
     * Initialize resource model
     * @return void
     */
    public function _construct()
    {
        $this->_init(ResourceModel\Font::class);
    }

    /**
     * Returns font name
     *
     * @return string
     */
    public function getFontName()
    {
        return $this->getData('font_name');
    }

    /**
     * Sets font name
     *
     * @param string $fontName
     *
     * @return $this
     */
    public function setFontName($fontName)
    {
        $this->setData('font_name', $fontName);

        return $this;
    }

    /**
     * Returns font type
     *
     * @return string
     */
    public function getFontType()
    {
        return $this->getData('font_type');
    }

    /**
     * Sets font type
     *
     * @param string $fontType
     *
     * @return $this
     */
    public function setFontType($fontType)
    {
        $this->setData('font_type', $fontType);

        return $this;
    }

    /**
     * Returns font file name
     *
     * @return string
     */
    public function getFontFileName()
    {
        return $this->getData('font_file');
    }

    /**
     * Sets font file name
     *
     * @param string $fontFileName
     *
     * @return $this
     */
    public function setFontFileName($fontFileName)
    {
        $this->setData('font_file', $fontFileName);

        return $this;
    }

    /**
     * Return font file url
     *
     * @return string
     */
    public function getFontFileUrl()
    {
        try {
            return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'svg_fonts/'.$this->getFontFileName();
        } catch (NoSuchEntityException $e) {
            return '';
        }
    }

    /**
     * Returns font file path
     *
     * @return string
     */
    public function getFontFilePath()
    {
        try {
            return $this->directoryList->getPath(DirectoryList::MEDIA).'/svg_fonts/'.$this->getFontFileName();
        } catch (FileSystemException $e) {
            return '';
        }
    }

    /**
     * Deletes a font
     *
     * @return AbstractModel|void
     *
     * @throws \Exception
     */
    public function delete()
    {
        parent::delete();

        $fontFile = $this->getFontFilePath();

        if (file_exists($fontFile)) {
            unlink($fontFile);
        }
    }
}

