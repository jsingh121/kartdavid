<?php

namespace Bison\CustomizableProducts\Controller\Adminhtml\Inspirations;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Bison\CustomizableProducts\Helper\Svg;
use Bison\CustomizableProducts\Model\Inspiration;

/**
 * Class Upload
 * @package Bison\CustomizableProducts\Controller\Adminhtml\Inspirations
 */
class Upload extends Action
{
    /** @var UploaderFactory */
    protected $uploaderFactory;

    /** @var DirectoryList */
    protected $directoryList;

    /** @var Svg */
    protected $svgHelper;

    /** @var Inspiration */
    protected $inspiration;

    /**
     * Upload constructor.
     * @param Action\Context $context
     * @param UploaderFactory $uploaderFactory
     * @param DirectoryList $directoryList
     * @param Svg $svgHelper
     * @param Inspiration $inspiration
     */
    public function __construct(
        Action\Context $context,
        UploaderFactory $uploaderFactory,
        DirectoryList $directoryList,
        Svg $svgHelper,
        Inspiration $inspiration
    )
    {
        parent::__construct($context);
        $this->uploaderFactory = $uploaderFactory;
        $this->directoryList = $directoryList;
        $this->svgHelper = $svgHelper;
        $this->inspiration = $inspiration;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function execute()
    {
        $file = $this->_request->getFiles('image');
        $svgContent = file_get_contents($file['tmp_name']);

        if ($this->svgHelper->hasScriptTags($svgContent)) {
            $this->svgHelper->removeScriptTags($svgContent, $file['tmp_name']);
        }

        $result = $this->uploadFile($file);
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);

    }

    /**
     * Upload inspiration file
     * @param $file
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function uploadFile($file)
    {
        $destinationPath = $this->directoryList->getPath(DirectoryList::MEDIA) . '/inspirations/';

        try {
            $uploader = $this->uploaderFactory->create(['fileId' => $file]);
            $uploader->setAllowCreateFolders(true);
            $uploader->setAllowRenameFiles(true);
            $uploader->setAllowedExtensions(['svg']);
            $result = $uploader->save($destinationPath);
            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $result;
    }
}