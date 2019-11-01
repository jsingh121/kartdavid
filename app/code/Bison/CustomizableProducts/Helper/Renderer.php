<?php

namespace Bison\CustomizableProducts\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Registry;
use Magento\Catalog\Model\Product\Option;

/**
 * Class Renderer
 * @package Bison\CustomizableProducts\Helper
 */
class Renderer extends AbstractHelper
{
    const BLOCK = \Bison\CustomizableProducts\Block\Customizer\Inputs::class;
    const TEMPLATES_PATH = 'Bison_CustomizableProducts::product/';

    /** @var \Magento\Framework\View\Element\BlockInterface */
    protected $resultPageFactory;

    /** @var Registry */
    protected $registry;

    /** @var Option */
    protected $option;

    /**
     * Renderer constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param Option $option
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        Option $option
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory->create()->getLayout()->createBlock(self::BLOCK);
        $this->registry = $registry;
        $this->option = $option;
    }

    /**
     * Return view html content.
     * @param string $type
     * @return string
     */
    public function getView(string $type) : string
    {
        $template = $this->getTemplatePathByType($type);

        return $this->resultPageFactory->setTemplate($template)->toHtml();
    }

    /**
     * Returns template path.
     * @param string $type
     * @return string
     */
    private function getTemplatePathByType(string $type) : string
    {
        return self::TEMPLATES_PATH . str_replace('-', '/', $type) . '.phtml';
    }
}