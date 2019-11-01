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
 * Class Delete
 * @package Bison\CustomizableProducts\Controller\Design
 */
class Delete extends Action
{
    const ERROR_MESSAGE = 'There was an error while removing your design. Please reload page and try again.';

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

    /** @var \Magento\Framework\Controller\Result\Json */
    protected $resultJsonFactory;

    /** @var array */
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
        $id = $this->_request->getParam('design_id');
        if (!$id) {
            $result = [
                'error_status' => '1',
                'message' => self::ERROR_MESSAGE
            ];
        } else {
            try {
                $this->design->load($id)->delete();
            } catch (\Exception $e) {
                $result = [
                    'error_status' => '1',
                    'message' => self::ERROR_MESSAGE
                ];
            }
        }

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        if (isset($result)) {
            $resultJson->setData($result);
        }

        return $resultJson;
    }

}