<?php

namespace Bison\CustomizableProducts\Controller\Design;

use Magento\Framework\App\Action\Action;

/**
 * Class Index
 * @package Bison\CustomizableProducts\Controller\Design
 */
class Index extends Action
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}