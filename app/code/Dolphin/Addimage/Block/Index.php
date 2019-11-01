<?php
namespace Dolphin\Addimage\Block;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Index extends \Magento\Framework\View\Element\Template
{
    protected $_productloader;
  		
  	public function __construct(
  		\Magento\Backend\Block\Template\Context $context,
  		\Magento\Catalog\Model\ProductFactory $_productloader,
  		array $data = []
  	)
  	{
  		$this->_productloader = $_productloader;
  		parent::__construct($context, $data);
  	}

  	public function getLoadProduct($id)
    {
      return $this->_productloader->create()->load($id);
    }
  	
    public function getProductId()
    {
      return $this->getRequest()->getParam('product');
    }
}