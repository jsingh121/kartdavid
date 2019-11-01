<?php 
namespace Cust\Linky\Observer;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class CustomerPostLogOutObserver implements ObserverInterface
{

    public function execute(EventObserver $observer)
    {
        // echo "CustomerPostLogOutObserver";
        // exit();
        exec('php bin/magento cache:flush');
    }
}

?>