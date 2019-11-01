<?php
namespace Cust\Linky\Observer;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Event\ObserverInterface;
class Topmenu implements ObserverInterface
{
    public function __construct()
    {
    
    }
    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {



        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $burl = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);



        /** @var \Magento\Framework\Data\Tree\Node $menu */
        $menu = $observer->getMenu();
        $tree = $menu->getTree();
        $data = [
            'name'      => __('Custom Designs'),
            'id'        => 'customdesigns',
            'url'       => $burl.'custom-designs'

        ];

        $data1 = [
            'name'      => __('About'),
            'id'        => 'abou',
            'url'       => $burl.'about-us'
        ];

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->get('Magento\Framework\Session\SessionManager');

//echo "<pre>";
//print_r($customerSession->getData());
//die("sdsa");

        if(isset($customerSession->getData("visitor_data")['do_customer_login'])) {
                $data2 = [
                    'name'      => __('SignOut'),
                    'id'        => 'someque-id-here',
                    'url'       => $burl.'customer/account/logout'
                ];

               // exec('php bin/magento cache:flush');
        }
        else {
                $data2 = [
                    'name'      => __('Register / Sign in'),
                    'id'        => 'someque-id-here',
                    'url'       => $burl.'customer/account/login'
                ];    
              //  exec('php bin/magento cache:flush');
        }

        $node = new Node($data, 'id', $tree, $menu);
        $node1 = new Node($data1, 'id', $tree, $menu);
        $node2 = new Node($data2, 'id', $tree, $menu);
        $menu->addChild($node);
        $menu->addChild($node1);
        $menu->addChild($node2);
        return $this;
    }
}