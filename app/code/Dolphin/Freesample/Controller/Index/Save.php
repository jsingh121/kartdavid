<?php

namespace Dolphin\Freesample\Controller\Index;
use Magento\Framework\Controller\ResultFactory;

class Save extends \Magento\Framework\App\Action\Action
{
    private static $_siteVerifyUrl = "https://www.google.com/recaptcha/api/siteverify?";
    private $_secret;
    private static $_version = "php_1.0";

	var $gridFactory;
    protected $objectManager;
    protected $_storeManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Dolphin\Freesample\Model\GridFactory $gridFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
         \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        parent::__construct($context);
        $this->gridFactory = $gridFactory;
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
    }


	/*public function execute(){
    	$data = $this->getRequest()->getPostValue();
        $rowData = $this->gridFactory->create();
        $rowData->setData($data);
        if($rowData->save()){
            $this->messageManager->addSuccessMessage(__('Your data is saved successfully.'));
        }else{
            $this->messageManager->addErrorMessage(__('Data was not saved.'));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }*/

    public function execute()
    {   

        $captcha = $this->getRequest()->getParam('g-recaptcha-response');
        $secret = "6Lf-Pr8UAAAAAMhe1AkBp4-gFxeyZ3azcIcjD48H";
        $response = null;
        $path = self::$_siteVerifyUrl;
        $dataC = array (
        'secret' => $secret,
        'remoteip' => $_SERVER["REMOTE_ADDR"],
        'v' => self::$_version,
        'response' => $captcha
        );
        $req = "";
        foreach ($dataC as $key => $value) {
             $req .= $key . '=' . urlencode(stripslashes($value)) . '&';
        }

        $req = substr($req, 0, strlen($req)-1);
        $response = file_get_contents($path . $req);
        $answers = json_decode($response, true);
        if(trim($answers ['success']) == true) {
            $data = $this->getRequest()->getPostValue();
			$rowData = $this->gridFactory->create();
			$rowData->setData($data);
			$name = $data['name'];
			$email = $data['email'];
			$street = $data['street'];
			$city = $data['city'];
			$postal_code = $data['postal_code'];
			$country = $data['country'];
			if($rowData->save()){
				$this->_objectManager->create('Dolphin\Freesample\Helper\Email')->createandsendml($name,$email,$street,$city,$postal_code,$country);
				//$this->messageManager->addSuccessMessage(__('Your data has been sent successfully.'));
			}else {
				$this->messageManager->addErrorMessage(__('Data was not saved.'));
			}
			$resultRedirect = $this->resultRedirectFactory->create();
			$redirecturl = str_replace("?popup=1", "", $this->_redirect->getRefererUrl());
			$resultRedirect->setUrl($redirecturl."?popup=1&message=We have recieved your request for a sample pack!");
			return $resultRedirect;
        }else{
            $this->messageManager->addErrorMessage(__('Captcha Not valid.'));
			$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
			$resultRedirect->setUrl($this->_redirect->getRefererUrl());
			return $resultRedirect;
        }
    }

}

?>