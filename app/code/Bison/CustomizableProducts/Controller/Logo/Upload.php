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

class Upload extends Action
{
    const FILE_INPUT_NAME = 'upload-logo';
    const ALLOWED_EXTENSIONS = ['png', 'svg', 'jpg', 'jpeg'];

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

    /** @var array */
    protected $result = [];

    /**
     * Logo helper
     *
     * @var \Bison\CustomizableProducts\Helper\Logo
     */
    protected $logoHelper;

    /**
     * Upload constructor.
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param DirectoryList $directoryList
     * @param UploaderFactory $uploaderFactory
     * @param Logo $logo
     * @param Session $customerSession
     * @param \Bison\CustomizableProducts\Helper\Logo $logoHelper
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        DirectoryList $directoryList,
        UploaderFactory $uploaderFactory,
        Logo $logo,
        Session $customerSession,
        \Bison\CustomizableProducts\Helper\Logo $logoHelper
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory->create();
        $this->directoryList = $directoryList;
        $this->uploaderFactory = $uploaderFactory;
        $this->logo = $logo;
        $this->customerSession = $customerSession;
        $this->logoHelper = $logoHelper;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $files = $this->_request->getFiles(self::FILE_INPUT_NAME);
        if (!$files) {
            $resultJson->setData([
                'error' => 'Please upload at least one file.'
            ]);

            return $resultJson;
        }

        $this->uploadLogos($files);

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($this->result);
        return $resultJson;
    }

    /**
     * Upload logo
     * @param $files
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function uploadLogos($files)
    {
        $destinationPath = $this->directoryList->getPath(DirectoryList::MEDIA) . '/user_logo/';
        foreach ($files as $file) {
            if (empty($file['name'])) {
                continue;
            }

            try {
                $uploader = $this->uploaderFactory->create(['fileId' => $file]);
                $uploader->setAllowCreateFolders(true);
                $uploader->setAllowRenameFiles(true);
                $uploader->setAllowedExtensions(self::ALLOWED_EXTENSIONS);
                $result = $uploader->save($destinationPath);

                $this->saveLogo($result);
            } catch (\Exception $e) {
                $this->result['error'] = $e->getMessage();
            }
        }
    }

    /**
     * Save logo model
     * @param $file
     */
    private function saveLogo($file)
    {
        $customerId = (int)($this->customerSession->getCustomerId() ?? 0);

        $this->logo
            ->setCustomerId($customerId)
            ->setLogoName($file['file'])
            ->setOrigName($file['name']);

        try {
            $this->logo->save();

            list($width, $height) = $this->logoHelper->getLogoDimension($this->logo->getLogoFilePath());

            $this->result = [
                'success' => "File {$file['name']} was saved properly",
                'url' => $this->logo->getLogoFileUrl(),
                'type' => $this->logo->getImageContentType(),
                'id' => $this->logo->getId(),
                'name' => $this->logo->getOrigName(),
                'width' => $width,
                'height' => $height
            ];
        } catch (\Exception $e) {
            $this->result['error'][] = "There was a problem while saving {$file['name']}";
        }

        $this->logo->unsetData();
    }
}