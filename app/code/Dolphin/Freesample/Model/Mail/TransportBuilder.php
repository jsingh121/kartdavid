<?php
 
 
namespace Dolphin\Freesample\Model\Mail;
 
class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
  public function addAttachment($file, $name)
    {
    	if($file){
        $this->message->createAttachment(
             file_get_contents($file),
             \Zend_Mime::TYPE_OCTETSTREAM,
			 \Zend_Mime::DISPOSITION_ATTACHMENT,
			 \Zend_Mime::ENCODING_BASE64,
		     basename($name)
        );
        return $this;
    	}
    	else{
    		return false;
    	}
    }
    
}
