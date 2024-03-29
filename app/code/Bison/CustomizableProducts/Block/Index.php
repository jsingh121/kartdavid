<?php
namespace Bison\CustomizableProducts\Block;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Index extends \Magento\Framework\View\Element\Template
{
    protected $_registry;
    protected $_productloader;
    protected $_quote;
	protected $quoteRepository;
  		
  	public function __construct(
  	  \Magento\Backend\Block\Template\Context $context,
      \Magento\Framework\Registry $registry,
  	  \Magento\Catalog\Model\ProductFactory $_productloader,
      \Magento\Quote\Model\Quote\Item $_quote,
	  \Magento\Quote\Model\QuoteRepository $quoteRepository,
  		array $data = []
  	)
  	{
      $this->_registry = $registry;
  	  $this->_productloader = $_productloader;
      $this->_quote = $_quote;
	  $this->quoteRepository = $quoteRepository;
  	  parent::__construct($context, $data);
  	}

    public function _prepareLayout()
    {
      return parent::_prepareLayout();
    }

    public function getCurrentProduct()
    {        
      return $this->_registry->registry('current_product');
    }
    
  	public function getLoadProduct($id)
    {
      return $this->_productloader->create()->load($id);
    }
  	
    public function getProductId()
    {
      $id = $this->getRequest()->getParam('item');
      $product = $this->_quote->load($id);
      $productId = $product->getProductId();     
      return $productId;
    }
    public function getQuoteId()
    {
      $quoteId = $this->getRequest()->getParam('quote');
      return $quoteId;
    }
	
	public function getItemId()
    {
      $itemId = $this->getRequest()->getParam('item');
      return $itemId;
    }
	
	public function getQuoteItems($quoteId)
    {
		 $itemid = $this->getItemId();
         try {
             $quote = $this->quoteRepository->get($quoteId);
             $items = $quote->getAllVisibleItems();
             foreach($items as $item){
				if($itemid == $item->getId()){
					$options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
					 $info = $options['info_buyRequest'];
				}
			}			
			return $info['options'];
         } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
             return [];
         }
    }

    public function getItemQty($quoteId)
        {
         $itemid = $this->getItemId();
             try {
                 $quote = $this->quoteRepository->get($quoteId);
                 $items = $quote->getAllVisibleItems();
                 foreach($items as $item){
            if($itemid == $item->getId()){
              $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
               $info = $options['info_buyRequest'];
            }
          }
          return $info['qty'];
             } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                 return [];
             }
        }
		public function getSvg($quoteId)
        {
         $itemid = $this->getItemId();
             try {
                 $quote = $this->quoteRepository->get($quoteId);
                 $items = $quote->getAllVisibleItems();
                 foreach($items as $item){					
					if($itemid == $item->getId()){
						return $item->getGeneratedSvg();
					}
				}         
             } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                 return [];
             }
        }

}