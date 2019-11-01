<?php

namespace Bison\SvgFonts\Controller\Adminhtml\Font;

use Bison\SvgFonts\Model\FontFactory;
use Magento\Backend\App\Action;

class Delete extends Action
{
    /**
     * Font factory
     *
     * @var FontFactory
     */
    protected $fontFactory;

    /**
     * Delete constructor.
     *
     * @param Action\Context $context
     * @param FontFactory $fontFactory
     */
    public function __construct(
        Action\Context $context,
        FontFactory $fontFactory
    ) {
        parent::__construct($context);

        $this->fontFactory = $fontFactory;
    }

    /**
     * Deletes a font
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $id   = $this->getRequest()->getParam('id', 0);

        if (!$id) {
            $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl('*/*/index'));
        }

        $font = $this->fontFactory->create()->load($id);

        try {
            $font->delete();

            $this->messageManager->addSuccessMessage(__('Font has been deleted.'));
            $this->getResponse()->setRedirect($this->getUrl('*/*/index'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('An error has occurred while trying to remove a font. Please try again later.'));
            $this->getResponse()->setRedirect($this->getUrl('*/*/*'));
        }

    }

}
