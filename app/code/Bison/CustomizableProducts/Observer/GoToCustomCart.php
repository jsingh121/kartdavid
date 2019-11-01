<?php
namespace Bison\CustomizableProducts\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class GoToCustomCart implements ObserverInterface
{

    protected $uri;
    protected $responseFactory;
    protected $_urlinterface;
	protected $_checkoutSession;
    protected $_productloader;
    protected $_storeManager;

 public function __construct(
        \Zend\Validator\Uri $uri,
        \Magento\Framework\UrlInterface $urlinterface,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\App\RequestInterface $request,
		\Magento\Checkout\Model\Session $_checkoutSession,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->uri = $uri;
        $this->_urlinterface = $urlinterface;
        $this->responseFactory = $responseFactory;
        $this->_request = $request;
		$this->_checkoutSession = $_checkoutSession;
        $this->_productloader = $_productloader;
        $this->_storeManager = $storeManager;
    }

    public function execute(Observer $observer)
    {
		$quote = $this->_checkoutSession->getQuote();		
		$cartData = $quote->getAllVisibleItems();		
		$maxid =0;
		foreach($cartData as $item){
			$maxid = $item->getId();
            $product_id = $item->getProductId();
            $productData = $this->_productloader->create()->load($product_id);
            $productCustomAdd = $productData->getCustomAddcart();
		}
		//echo $cartDataCount = count( $cartData );
		//echo $maxid.'<br>';
        if($productCustomAdd == 1){
            $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
            $url = $baseUrl.'customiser/index/index/?item='.$maxid.'&quote='.$quote->getId();
            $resultRedirect = $this->responseFactory->create();
            $resultRedirect->setRedirect($url)->sendResponse('200');
            exit();
        }
    }
}