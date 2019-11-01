<?php

namespace Bison\CustomizableProducts\Controller\Design;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Filesystem;
use Bison\CustomizableProducts\Model\DesignFactory;
use Bison\CustomizableProducts\Model\Design;
use Bison\CustomizableProducts\Helper\Cookie;
use Magento\Framework\Json\Helper\Data;

/**
 * Class Save
 * @package Bison\CustomizableProducts\Controller\Design
 */
class Save extends Action
{
    const BASE_FILE_NAME = 'custom_design';
    const FILE_EXTENSION = '.svg';
    const ERROR_MESSAGE = 'There was an error while saving you design. Please reload page and check again.';
    const SUCCESS_MESSAGE = 'Your design has been saved';

    /** @var DirectoryList */
    protected $directoryList;

    /** @var Filesystem */
    protected $filesystem;

    /** @var DesignFactory */
    protected $designFactory;

    /** @var Design */
    protected $design;

    /** @var Session */
    protected $customerSession;

    /** @var \Magento\Framework\Controller\Result\Json  */
    protected $resultJsonFactory;

    /** @var array*/
    protected $result;

    /** @var Cookie */
    protected $cookieHelper;

    /** @var Data */
    protected $jsonHelper;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param DirectoryList $directoryList
     * @param Filesystem $filesystem
     * @param DesignFactory $designFactory
     * @param Design $design
     * @param Session $customerSession
     * @param JsonFactory $resultJsonFactory
     * @param Cookie $cookie
     * @param Data $jsonHelper
     */
    public function __construct(
        Context $context,
        DirectoryList $directoryList,
        Filesystem $filesystem,
        DesignFactory $designFactory,
        Design $design,
        Session $customerSession,
        JsonFactory $resultJsonFactory,
        Cookie $cookie,
        Data $jsonHelper
    )
    {
        parent::__construct(
            $context
        );

        $this->directoryList = $directoryList;
        $this->filesystem = $filesystem;
        $this->designFactory = $designFactory;
        $this->design = $design;
        $this->customerSession = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory->create();
        $this->cookieHelper = $cookie;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $params = $this->_request->getParams();
        $this->saveSvgToFile($params['svg']);

        if (!isset($this->result['error_status'])) {
            $this->saveDesign($params['product_id']);
        }

        if (isset($this->result['error_status'])) {
            $this->result['message'] = self::ERROR_MESSAGE;
        } else {
            $this->result['message'] = self::SUCCESS_MESSAGE;
        }

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($this->result);
        return $resultJson;
    }

    /**
     * Save svg content to media file.
     *
     * @param $svg
     */
    private function saveSvgToFile(string $svg)
    {
        try {
            $svg = preg_replace(['/NS\d+:href/i', '/xmlns:NS\d+/i', '/<style xmlns="http:\/\/www.w3.org\/1999\/xhtml">/i'], ['xlink:href', 'xmlns:xlink', '<style>'], $svg);
            $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)->writeFile("user_design/{$this->generateFileName()}", $svg);
        } catch (\Exception $e) {
            $this->result['error_status'] = 1;
        }
    }

    /**
     * Save design model
     *
     * @param int $productId
     */
    private function saveDesign(int $productId)
    {
        $customerId = (int)($this->customerSession->getCustomerId() ?? 0);

        $this->design
            ->setProductId($productId)
            ->setCustomerId($customerId)
            ->setDesignName($this->generateFileName());

        try {
            $this->design->save();
            $content = $this->getCookieContent();
            if (!$customerId) {
                $this->cookieHelper->set($content);
            }
        } catch (\Exception $e) {
            $this->result['error_status'] = 1;
        }
    }

    /**
     * Generate file name basing on last entity id
     *
     * @return string
     */
    private function generateFileName(): string
    {
        $design = $this->designFactory->create();
        $lastId = $design->getCollection()->load()->getLastItem()->getId();
        $id = $lastId + 1;

        return self::BASE_FILE_NAME . $id . self::FILE_EXTENSION;
    }

    /**
     * Create cookie encoded content
     *
     * @return string
     */
    private function getCookieContent(): string
    {
        $content = [];
        $designId = $this->design->getId();
        $jsonContent = $this->cookieHelper->get();

        if ($jsonContent) {
            $content = $this->jsonHelper->jsonDecode($jsonContent);
        }

        $content[] = $designId;
        return $this->jsonHelper->jsonEncode($content);
    }
}
