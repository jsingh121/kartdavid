<?php

namespace Dolphin\Freesample\Controller\Adminhtml\Data;

class Index extends \Magento\Backend\App\Action
{
    private $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Dolphin_Freesample::info');
        $resultPage->getConfig()->getTitle()->prepend(__('Sample Order'));
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Dolphin_Freesample::info');
    }
}
