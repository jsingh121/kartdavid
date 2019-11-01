<?php
namespace Dolphin\Addimage\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class GoToCustomCart implements ObserverInterface
{

    protected $uri;
    protected $responseFactory;
    protected $_urlinterface;

 public function __construct(
        \Zend\Validator\Uri $uri,
        \Magento\Framework\UrlInterface $urlinterface,
        \Magento\Framework\App\ResponseFactory $responseFactory,
         \Magento\Framework\App\RequestInterface $request
    ) {
        $this->uri = $uri;
        $this->_urlinterface = $urlinterface;
        $this->responseFactory = $responseFactory;
        $this->_request = $request;
    }

    public function execute(Observer $observer)
    {
        $resultRedirect = $this->responseFactory->create();
        $resultRedirect->setRedirect($this->_urlinterface->getUrl('checkout/cart'))->sendResponse('200');
        exit();
    }
}