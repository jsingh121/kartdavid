<?php

namespace Bison\CustomizableProducts\Controller\Adminhtml\Logo;

use Bison\CustomizableProducts\Model\PredefinedLogo;
use Bison\CustomizableProducts\Model\PredefinedLogoFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;

class Edit extends Action
{
    /**
     * Logo factory
     *
     * @var PredefinedLogoFactory
     */
    protected $logoFactory;

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
     * @param PredefinedLogoFactory $logoFactory
     */
    public function __construct(
        Action\Context $context,
        Registry $registry,
        PredefinedLogoFactory $logoFactory
    ) {
        parent::__construct($context);

        $this->registry    = $registry;
        $this->logoFactory = $logoFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $logo = $this->loadLogo();
        $this->saveLogo($logo);

        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }

    /**
     * Loads font from request data and add it to registry
     *
     * @return PredefinedLogo
     */
    protected function loadLogo()
    {
        $id   = $this->getRequest()->getParam('id', 0);

        $logo = $this->logoFactory->create();
        if ($id) {
            $logo->load($id);
        }

        $this->registry->register('current_logo', $logo);

        return $logo;
    }

    /**
     * Saves a font
     *
     * @param PredefinedLogo $logo
     */
    protected function saveLogo(PredefinedLogo $logo)
    {
        $name = $this->getRequest()->getParam('name', '');
        $categoryId = $this->getRequest()->getParam('category_id', 0);
        $file = $this->getRequest()->getParam('logo', []);

        if (empty($name)) {
            return ;
        }

        $logo
            ->setName($name)
            ->setCategoryId($categoryId);

        if (array_key_exists('file', $file[0])) {
            $logo->setFilename($file[0]['file']);
        }

        try {
            $logo->save();

            $this->messageManager->addSuccessMessage(__('Logo has been saved.'));
            $redirectUrl = $this->getUrl('*/*/index');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $redirectUrl = $this->getUrl('*/*/*');
        }

        $this->getResponse()->setRedirect($redirectUrl);
    }

}
