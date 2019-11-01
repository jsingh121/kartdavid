<?php

namespace Bison\CustomizableProducts\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Bison\CustomizableProducts\Helper\Cookie;
use \Magento\Framework\Model\AbstractModel;

class Design extends AbstractModel
{
    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $storeManager;

    /**
     * Directory list
     *
     * @var DirectoryList
     */
    protected $directoryList;

    protected $cookieHelper;

    /**
     * Design constructor.
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
        Cookie $cookie,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->storeManager = $storeManager;
        $this->directoryList = $directoryList;
        $this->cookieHelper = $cookie;
    }

    /**
     * Initialize resource model
     * @return void
     */
    public function _construct()
    {
        $this->_init(ResourceModel\Design::class);
    }

    /**
     * Returns design name
     * @return string
     */
    public function getDesignName()
    {
        return $this->getData('name');
    }

    /**
     * Sets design file name
     * @param string $designName
     * @return $this
     */
    public function setDesignName(string $designName)
    {
        $this->setData('name', $designName);
        return $this;
    }

    /**
     * Returns product ID
     * @return int
     */
    public function getProductId()
    {
        return $this->getData('product_id');
    }

    /**
     * Sets product ID
     * @param string $productId
     * @return $this
     */
    public function setProductId(string $productId)
    {
        $this->setData('product_id', $productId);
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
     * Return design file url
     * @return string
     */
    public function getDesignFileUrl()
    {
        try {
            return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'user_design/' . $this->getDesignName();
        } catch (NoSuchEntityException $e) {
            return '';
        }
    }

    /**
     * Returns design file path
     *
     * @return string
     */
    public function getDesignFilePath()
    {
        try {
            return $this->directoryList->getPath(DirectoryList::MEDIA) . '/user_design/' . $this->getDesignName();
        } catch (FileSystemException $e) {
            return '';
        }
    }

    /**
     * Deletes a design
     *
     * @return AbstractModel|void
     *
     * @throws \Exception
     */
    public function delete()
    {
        parent::delete();

        $DesignFile = $this->getDesignFilePath();
        if (file_exists($DesignFile)) {
            unlink($DesignFile);
        }
    }

    /**
     * Get dessign collection associated with given product and customer
     * @param $productId
     * @param null $customerId
     * @return $this
     */
    public function getAssociatedDesigns($productId, $customerId = null)
    {
        if ($customerId) {
            $collection = $this->getCollection()
                ->addFieldToFilter('customer_id', ['eq' => $customerId]);

            if ($productId) {
                $collection->addFieldToFilter('product_id', ['eq' => $productId]);
            }

            return $collection;
        }

        $designIds = $this->cookieHelper->get();

        if ($designIds) {
            $designIds = json_decode($designIds);
            return $this->getCollection()
                ->addFieldToFilter('id', ['in' => $designIds])
                ->addFieldToFilter('product_id', ['eq' => $productId]);
        }
    }
}

