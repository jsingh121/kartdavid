<?php

namespace Bison\CustomizableProducts\Observer;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote\Item;

class SaveGeneratedSvgToQuoteItem implements ObserverInterface
{
    /**
     * Request
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Directory list
     *
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * File system
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * SaveGeneratedSvgToQuoteItem constructor
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @param DirectoryList $directoryList
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        DirectoryList $directoryList,
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->request =  $request;
        $this->directoryList = $directoryList;
        $this->filesystem = $filesystem;
    }

    /**
     * Saves generated svg design to quote item
     *
     * @param Observer $observer
     *
     * @return void
     *
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        $quoteItem = $this->getQuoteItem($observer);
        $svg = $this->request->getParam('productsvg_img', '');
        /*$svg = preg_replace(['/NS\d+:href/i', '/xmlns:NS\d+/i', '/<style xmlns="http:\/\/www.w3.org\/1999\/xhtml">/i'], ['xlink:href', 'xmlns:xlink', '<style>'], $svg);*/		
        //$directory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
		$directory = 'pub/media/';
        $filename = 'quote-svg/'.$quoteItem->getQuoteId().'-'.microtime(true).'.svg';
		if($svg != ""){		
			copy($svg,$directory.$filename);
			$quoteItem->setData('generated_svg', $filename);
		}
		//exit;
        //$directory->writeFile($filename, $svg);
        //$quoteItem->setData('generated_svg', $filename);
    }

    /**
     * Return quote item
     *
     * @param Observer $observer
     *
     * @return Item
     */
    public function getQuoteItem(Observer $observer){
        return $observer->getEvent()->getData('quote_item');
    }

}
