<?php

namespace Bison\SvgFonts\Controller\Adminhtml\Font;

use Bison\SvgFonts\Model\Font;
use Bison\SvgFonts\Model\FontFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;

class Edit extends Action
{
    /**
     * Font factory
     *
     * @var FontFactory
     */
    protected $fontFactory;

    /**
     * Registry
     *
     * @var Registry
     */
    protected $registry;

    /**
     * Edit constructor.
     *
     * @param Action\Context $context
     * @param Registry $registry
     * @param FontFactory $fontFactory
     */
    public function __construct(
        Action\Context $context,
        Registry $registry,
        FontFactory $fontFactory
    ) {
        parent::__construct($context);

        $this->registry    = $registry;
        $this->fontFactory = $fontFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $font = $this->loadFont();
        $this->saveFont($font);

        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }

    /**
     * Loads font from request data and add it to registry
     *
     * @return Font
     */
    protected function loadFont()
    {
        $id   = $this->getRequest()->getParam('id', 0);

        $font = $this->fontFactory->create();
        if ($id) {
            $font->load($id);
        }

        $this->registry->register('current_font', $font);

        return $font;
    }

    /**
     * Saves a font
     *
     * @param Font $font
     */
    protected function saveFont(Font $font)
    {
        $name = $this->getRequest()->getParam('font_name', '');
        $file = $this->getRequest()->getParam('font_file', []);
        $type = $this->getRequest()->getParam('font_type');

        if (empty($name) || empty($file)) {
            return ;
        }

        $font->setFontname($name);
        $font->setFontType($type);

        if (array_key_exists('file', $file[0])) {
            $font->setFontFileName($file[0]['file']);
        }

        try {
            $font->save();

            $this->messageManager->addSuccessMessage(__('Font has been saved.'));
            $redirectUrl = $this->getUrl('*/*/index');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $redirectUrl = $this->getUrl('*/*/*');
        }

        $this->getResponse()->setRedirect($redirectUrl);
    }

}
