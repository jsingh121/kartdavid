<?php 
namespace Cust\Linky\Observer;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class CustomerPreLogOutObserver implements ObserverInterface
{

    public function execute(EventObserver $observer)
    {
        echo "CustomerPreLogOutObserver";
        exit();
        exec('php bin/magento cache:flush');

    }
}

?>