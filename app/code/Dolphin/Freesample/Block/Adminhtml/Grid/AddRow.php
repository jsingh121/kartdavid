<?php
namespace Dolphin\Freesample\Block\Adminhtml\Grid;
class AddRow extends \Magento\Backend\Block\Widget\Form\Container
{

	public function __construct(\Magento\Backend\Block\Widget\Context $context,
	\Magento\Framework\Registry $registry,array $data = []) 
	{	
		$this->_coreRegistry = $registry;
		parent::__construct($context, $data);
	}
	protected function _construct()
	{
		$this->_objectId = 'row_id';
		$this->_blockGroup = 'Dolphin_Freesample';
		$this->_controller = 'adminhtml_grid';
		parent::_construct();
		if ($this->_isAllowedAction('Dolphin_Freesample::info')) {
			$this->buttonList->update('save', 'label', __('Save'));
		}
		else {
			$this->buttonList->remove('save');
		}
		$this->buttonList->remove('reset');
	}
	protected function _isAllowedAction($resourceId)
	{
		return $this->_authorization->isAllowed($resourceId);
	}
	public function getFormActionUrl()
	{
		if ($this->hasFormActionUrl()) {
			return $this->getData('form_action_url');
		}
		return $this->getUrl('*/*/save');
	}
}