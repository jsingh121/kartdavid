<?php

namespace Bison\CustomizableProducts\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Framework\Model\AbstractModel;

class Inspiration extends AbstractModel
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
     * Inspiration constructor.
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
        $this->_init(ResourceModel\Inspiration::class);
    }

    /**
     * Sets inspiration file name
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->setData('name', $name);
        return $this;
    }

    /**
     * Returns inspiration file name
     * @return string
     */
    public function getName()
    {
        return $this->getData('name');
    }

    /**
     * Sets product ID
     * @param int $productId
     * @return $this
     */
    public function setProductId(int $productId)
    {
        $this->setData('product_id', $productId);
        return $this;
    }

    /**
     * Get product ID
     * @return mixed
     */
    public function getProductId()
    {
        return $this->getData('product_id');
    }

    /**
     * Return inspiration file url
     * @return string
     */
    public function getInspirationFileUrl()
    {
        try {
            return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'/inspirations/'.$this->getName();
        } catch (NoSuchEntityException $e) {
            return '';
        }
    }

    /**
     * Returns inspiration file path
     *
     * @return string
     */
    public function getInspirationFilePath()
    {
        try {
            return $this->directoryList->getPath(DirectoryList::MEDIA).'/inspirations/'.$this->getName();
        } catch (FileSystemException $e) {
            return '';
        }
    }

    /**
     * Deletes an inspiration file
     *
     * @return AbstractModel|void
     *
     * @throws \Exception
     */
    public function delete()
    {
        parent::delete();

        $inspirationFile = $this->getInspirationFilePath();
        if (file_exists($inspirationFile)) {
            unlink($inspirationFile);
        }
    }
}

