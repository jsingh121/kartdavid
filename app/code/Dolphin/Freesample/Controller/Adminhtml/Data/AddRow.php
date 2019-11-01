<?php
namespace Dolphin\Freesample\Controller\Adminhtml\Data;
use Magento\Framework\Controller\ResultFactory;	
class AddRow extends \Magento\Backend\App\Action
{
	protected $coreRegistry;
	protected $gridFactory;
	protected $_resultFactory;
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\Registry $coreRegistry,
		ResultFactory $resultFactory,
		\Dolphin\Freesample\Model\GridFactory $gridFactory
	) {
		parent::__construct($context);
		$this->coreRegistry = $coreRegistry;
		$this->gridFactory = $gridFactory;
		$this->_resultFactory = $resultFactory;
	}

	protected function _initAction()
	{
	// load layout, set active menu and breadcrumbs
	// /** @var \Magento\Backend\Model\View\Result\Page $resultPage * /
	$resultPage = $this->_resultFactory->create(ResultFactory::TYPE_PAGE);
	$resultPage->setActiveMenu(
	'Dolphin_Freesample::info')->addBreadcrumb(__('Ecommerce'),__('Ecommerce'))->addBreadcrumb(__('Manage Ecommerce'),__('Manage Ecommerce')
	);
	return $resultPage;
	}



	public function execute()
	{
		// 1. Get ID and create model
		$id = $this->getRequest()->getParam('id'); //Primary key column
		$model = $this->_objectManager->create('Dolphin\Freesample\Model\Grid');

		// 2. Initial checking
		if ($id) {
			$model->load($id);
			if (!$model->getId()) {
				$this->messageManager->addError(__('This ecommerce no longer exists.'));
				/* \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
				$resultRedirect = $this->resultRedirectFactory->create();
				return $resultRedirect->setPath('design/data/index');
			}
		}

		$data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
		if (!empty($data)) {
		$model->setData($data);
		}
		$this->coreRegistry->register('freesample', $model);

		$resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Page') : __('Select'),
            $id ? __('Edit Page') : __('Select')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Freesample'));
        /*$resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getTitle() : __('Manage Freesample'));*/

        return $resultPage;
    }

	public function _isAllowed()
	{
		return $this->_authorization->isAllowed('Dolphin_Freesample::info');
	}

	


}
