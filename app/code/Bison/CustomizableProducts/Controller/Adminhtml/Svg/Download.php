<?php

namespace Bison\CustomizableProducts\Controller\Adminhtml\Svg;

use Bison\CustomizableProducts\Helper\Svg;
use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Sales\Model\Order\ItemFactory;

/**
 * Class Download
 *
 * @package Bison\CustomizableProducts\Controller\Adminhtml\Svg
 */
class Download extends Action
{
    /**
     * Directory list
     *
     *  @var DirectoryList
     */
    protected $directoryList;

    /**
     * Order Item factory
     *
     * @var ItemFactory
     */
    protected $orderItemFactory;

    /**
     * Svg Helper
     *
     * @var Svg
     */
    protected $svgHelper;

    /**
     * Upload constructor.
     * @param Action\Context $context
     * @param DirectoryList $directoryList
     * @param ItemFactory $orderItemFactory
     * @param Svg $svgHelper
     */
    public function __construct(
        Action\Context $context,
        DirectoryList $directoryList,
        ItemFactory $orderItemFactory,
        Svg $svgHelper
    ) {
        parent::__construct($context);
        $this->directoryList = $directoryList;
        $this->orderItemFactory = $orderItemFactory;
        $this->svgHelper = $svgHelper;
    }

    /**
     * Returns generated svg file
     *
     * @return string
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function execute()
    {
        $itemId = $this->getRequest()->getParam('item_id', 0);

        if (!$itemId) {
            return ;
        }

        $orderItem = $this->orderItemFactory->create()->load($itemId);
        $filePath = $this->directoryList->getPath(DirectoryList::MEDIA).'/'.$orderItem->getData('generated_svg');

        if (file_exists($filePath) && is_readable($filePath)) {
            header('Content-type: image/svg+xml');
            header("Content-Disposition: attachment; filename={$orderItem->getOrder()->getRealOrderId()}-{$orderItem->getId()}.svg");

            $svgContent = file_get_contents($filePath);
            $svgContent = $this->svgHelper->clearForDownload($svgContent);
            die($svgContent);
        }
    }
}