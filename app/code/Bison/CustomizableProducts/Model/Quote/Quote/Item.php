<?php

namespace Bison\CustomizableProducts\Model\Quote\Quote;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Quote\Model\Quote\Item\OptionFactory;
use Magento\Quote\Model\Quote\Item\Compare;

class Item extends \Magento\Quote\Model\Quote\Item
{
    /**
     * Request
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Filesystem
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Sales\Model\Status\ListFactory $statusListFactory,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        OptionFactory $itemOptionFactory,
        Compare $quoteItemCompare,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $productRepository, $priceCurrency, $statusListFactory, $localeFormat, $itemOptionFactory, $quoteItemCompare, $stockRegistry, $resource, $resourceCollection, $data, $serializer);
        $this->request = $request;
        $this->filesystem = $filesystem;
    }

    /**
     * Check product representation in item
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return  bool
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function representProduct($product)
    {
        $represent = parent::representProduct($product);

        if ($represent) {
            $svg = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->readFile($this->getData('generated_svg'));
            return $svg === $this->request->getParam('generated_svg', '');
        }

        return $represent;
    }
}