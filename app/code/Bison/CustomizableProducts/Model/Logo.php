<?php

namespace Bison\CustomizableProducts\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Framework\Model\AbstractModel;

class Logo extends AbstractModel
{
    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $storeManager;

    /**
     * Directory list
     *
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * Logo constructor.
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
     * @return void
     */
    public function _construct()
    {
        $this->_init(ResourceModel\Logo::class);
    }

    /**
     * Returns logo name
     * @return string
     */
    public function getLogoName()
    {
        return $this->getData('name');
    }

    /**
     * Sets logo file name
     * @param string $logoName
     * @return $this
     */
    public function setLogoName(string $logoName)
    {
        $this->setData('name', $logoName);
        return $this;
    }

    /**
     * Returns original logo name
     * @return string
     */
    public function getOrigName()
    {
        return $this->getData('orig_name');
    }

    /**
     * Sets original logo file name
     * @param string $logoName
     * @return $this
     */
    public function setOrigName(string $logoName)
    {
        $this->setData('orig_name', $logoName);
        return $this;
    }

    /**
     * Sets customer id
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId(int $customerId)
    {
        $this->setData('customer_id', $customerId);
        return $this;
    }

    /**
     * Returns customer ID
     * @return int
     */
    public function getCustomerId()
    {
        return $this->getData('customer_id');
    }

    /**
     * Return logo file url
     * @return string
     */
    public function getLogoFileUrl()
    {
        try {
            return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'user_logo/'.$this->getLogoName();
        } catch (NoSuchEntityException $e) {
            return '';
        }
    }

    /**
     * Returns logo file path
     *
     * @return string
     */
    public function getLogoFilePath()
    {
        try {
            return $this->directoryList->getPath(DirectoryList::MEDIA).'/user_logo/'.$this->getLogoName();
        } catch (FileSystemException $e) {
            return '';
        }
    }

    /**
     * Return image content type
     *
     * @return string
     */
    public function getImageContentType()
    {
        $contentType = strtolower(pathinfo($this->getLogoFilePath(), PATHINFO_EXTENSION));

        switch ($contentType) {
            case 'gif':
                return 'image/gif';
                break;
            case 'png':
                return 'image/png';
                break;
            case 'jpg':
            case 'jpeg':
                return 'image/jpg';
                break;
            default:
                return '';
                break;
        }
    }

    /**
     * Deletes a logo
     *
     * @return AbstractModel|void
     *
     * @throws \Exception
     */
    public function delete()
    {
        parent::delete();

        $logoFile = $this->getLogoFilePath();
        if (file_exists($logoFile) && !is_dir($logoFile)) {
            unlink($logoFile);
        }
    }
}

