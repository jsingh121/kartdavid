<?php
namespace Dolphin\Freesample\Helper;
class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
	//const XML_PATH_EMAIL_DEMO = 'emaildemo/email/email_demo_template';
	protected $_inlineTranslation;
 
    protected $_transportBuilder;
 
    protected $_template;
 
    protected $_storeManager;
	 
	public function __construct(
	    \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Dolphin\Freesample\Model\Mail\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager
	)
	{
		parent::__construct($context);
		$this->_inlineTranslation = $inlineTranslation;
		$this->_transportBuilder = $transportBuilder;
		$this->_storeManager = $storeManager;
	}
  
 	public function createandsendml($name,$email,$street,$city,$postal_code,$country)
	{
		$setTemplate = $this->scopeConfig->getValue('customization/email/emailtemplate', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		//$pdfFile = $filefullpath;
		$from1 = $this->scopeConfig->getValue('customization/email/from', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$to1 = $this->scopeConfig->getValue('customization/email/to', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$subject1 = $this->scopeConfig->getValue('customization/email/subject', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		
		$emailTemplateVariables=array();
		$emailTemplateVariables['name'] = $name;
		$emailTemplateVariables['email'] = $email;
		$emailTemplateVariables['street'] = $street;
		$emailTemplateVariables['city'] = $city;
		$emailTemplateVariables['postal_code'] = $postal_code;
		$emailTemplateVariables['country'] = $country;
		$emailTemplateVariables['store'] = $this->_storeManager->getStore();
		$templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->_storeManager->getStore()->getId());
		$from = array('email' => $from1, 'name' => $subject1);
		$to = array($to1,$email);
		
		$transport = $this->_transportBuilder->setTemplateIdentifier($setTemplate)
		->setTemplateOptions($templateOptions)
		->setTemplateVars($emailTemplateVariables)
		->setFrom($from)
		->addTo($to)
		->getTransport();
		
		 try {
		 	//$transport = $this->_transportBuilder->getTransport();
		 	$transport->sendMessage();
		 } catch (\Exception $e) {
		 	echo $e->getMessage(); die;
		 }
	
	}   
}
