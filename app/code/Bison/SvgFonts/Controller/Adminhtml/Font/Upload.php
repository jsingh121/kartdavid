<?php

namespace Bison\SvgFonts\Controller\Adminhtml\Font;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\MediaStorage\Model\File\UploaderFactory;

class Upload extends Action
{
    const FILE_FIELD_NAME = 'font_file';

    const ALLOWED_EXTENSIONS = ['ttf'];

    /**
     * Directory list
     *
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * Uploader factory
     *
     * @var UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * Upload constructor.
     *
     * @param Action\Context $context
     * @param DirectoryList $directoryList
     * @param UploaderFactory $uploaderFactory
     */
    public function __construct(
        Action\Context $context,
        DirectoryList $directoryList,
        UploaderFactory $uploaderFactory
    ) {
        parent::__construct($context);

        $this->directoryList   = $directoryList;
        $this->uploaderFactory = $uploaderFactory;
    }

    /**
     * Uploads a font file
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function execute()
    {
        $destinationPath = $this->directoryList->getPath(DirectoryList::MEDIA) . '/svg_fonts/';
        $result = [];

        try {
            $uploader = $this->uploaderFactory->create(['fileId' => self::FILE_FIELD_NAME])
                ->setAllowCreateFolders(true)
                ->setAllowRenameFiles(true)
                ->setAllowedExtensions(self::ALLOWED_EXTENSIONS);

            $result = $uploader->save($destinationPath);

            if (!$result) {
                throw new LocalizedException(
                    __('File cannot be saved to path: $1', $destinationPath)
                );
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __($e->getMessage())
            );
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);
        return $resultJson;
    }

}
