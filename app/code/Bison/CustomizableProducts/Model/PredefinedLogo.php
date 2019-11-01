<?php

namespace Bison\CustomizableProducts\Model;

use \Magento\Framework\Model\AbstractModel;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Predefined Logo Class
 *
 * @package Bison\CustomizableProducts\Model
 *
 * @method PredefinedLogo setName(string $name)
 * @method string getName()
 * @method PredefinedLogo setCategoryId(int $categoryId)
 * @method int getCategoryId()
 * @method PredefinedLogo setFilename(string $filename)
 * @method string getFilename()
 */
class PredefinedLogo extends AbstractModel
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

    /**
     * PredefinedLogo class constructor
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param DirectoryList $directoryList
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
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
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(ResourceModel\PredefinedLogo::class);
    }

    /**
     * Returns logo url
     *
     * @return string
     */
    public function getLogoUrl()
    {
        try {
            return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'predefined-logo/'.$this->getFilename();
        } catch (NoSuchEntityException $e) {
            return '';
        }
    }

    /**
     * Returns logo path
     *
     * @return string
     */
    public function getLogoPath()
    {
        try {
            return $this->directoryList->getPath(DirectoryList::MEDIA).'/predefined-logo/'.$this->getFilename();
        } catch (FileSystemException $e) {
            return '';
        }
    }
}

