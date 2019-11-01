<?php

namespace Dolphin\Freesample\Controller\Adminhtml\Data;

class Save extends \Magento\Backend\App\Action
{
    var $gridFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Dolphin\Freesample\Model\GridFactory $gridFactory
    ) {
        parent::__construct($context);
        $this->gridFactory = $gridFactory;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            $this->_redirect('freesample/data/addrow');
            return;
        }
        try {
            $model = $this->_objectManager->create('Dolphin\Freesample\Model\Grid');
            $model->load($data['id'])->getData();
            $model->setData($data);
            $model->save();
            $this->messageManager->addSuccess(__('Row data has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('freesample/data/index');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Dolphin_Freesample::save');
    }
}
