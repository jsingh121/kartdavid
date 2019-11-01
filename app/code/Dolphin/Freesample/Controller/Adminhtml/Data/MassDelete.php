<?php
namespace Dolphin\Freesample\Controller\Adminhtml\Data;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Dolphin\Freesample\Model\ResourceModel\Grid\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;
class MassDelete extends \Magento\Backend\App\Action
{
	public $filter;
    public $_coreRegistry = null;
    public $resultPageFactory;
    protected $CollectionFactory;
    public function __construct(
    Action\Context $context,
    \Magento\Framework\View\Result\PageFactory $resultPageFactory,
    \Magento\Framework\Registry $registry,
    Filter $filter,
    CollectionFactory $CollectionFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->CollectionFactory = $CollectionFactory;  
        $this->filter = $filter;
        parent::__construct($context);
    }
    public function execute()
    {
        $items = $this->getRequest()->getParams('freesample');
        if (array_key_exists('excluded', $items)) {
        	try{
	            $itemModel = $this->_objectManager->create('Dolphin\Freesample\Model\Grid');

	            foreach ($itemModel->getCollection()->getData() as $items) {
                    $itemModel->load($items['id'])->delete();
	            }
	            $this->messageManager->addSuccess(__('All record(s) were deleted.'));
	        } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
            }
        }
        else{
            $itemIds = $items['selected'];
            foreach ($itemIds as $itemId) {
                try {

                    $itemModel = $this->_objectManager->create('Dolphin\Freesample\Model\Grid');
                    $a = $itemModel->load($itemId)->delete();

                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                }
            }
            $this->messageManager->addSuccess(__('A total of %1 record(s) were deleted.', count($itemIds)));
        }

    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
    $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    return $resultRedirect;

    }
}
    