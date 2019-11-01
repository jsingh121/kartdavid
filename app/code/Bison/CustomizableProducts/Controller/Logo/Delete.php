<?php

namespace Bison\CustomizableProducts\Controller\Logo;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Bison\CustomizableProducts\Model\Logo;
use Magento\Customer\Model\Session;

class Delete extends Action
{
       /** @var \Magento\Framework\Controller\Result\Json */
    private $resultJsonFactory;

    /** @var DirectoryList */
    private $directoryList;

    /** @var UploaderFactory */
    private $uploaderFactory;

    /** @var Logo */
    private $logo;

    /** @var Session */
    private $customerSession;

    /**
     * Delete constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param DirectoryList $directoryList
     * @param UploaderFactory $uploaderFactory
     * @param Logo $logo
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        DirectoryList $directoryList,
        UploaderFactory $uploaderFactory,
        Logo $logo,
        Session $customerSession
    )
    {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory->create();
        $this->directoryList = $directoryList;
        $this->uploaderFactory = $uploaderFactory;
        $this->logo = $logo;
        $this->customerSession = $customerSession;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $logoId = $this->_request->getParam('id');
        $result = [];

        $this->logo->load($logoId);

        try {
            $this->logo->delete();
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
        }

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);
        return $resultJson;
    }
}