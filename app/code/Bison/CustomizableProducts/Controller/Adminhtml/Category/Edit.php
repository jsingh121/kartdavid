<?php

namespace Bison\CustomizableProducts\Controller\Adminhtml\Category;

use Bison\CustomizableProducts\Model\LogoCategory;
use Bison\CustomizableProducts\Model\LogoCategoryFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;

class Edit extends Action
{
    /**
     * Font factory
     *
     * @var LogoCategoryFactory
     */
    protected $categoryFactory;

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
     * @param LogoCategoryFactory $categoryFactory
     */
    public function __construct(
        Action\Context $context,
        Registry $registry,
        LogoCategoryFactory $categoryFactory
    ) {
        parent::__construct($context);

        $this->registry    = $registry;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $category = $this->loadCategory();
        $this->saveCategory($category);

        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }

    /**
     * Loads font from request data and add it to registry
     *
     * @return LogoCategory
     */
    protected function loadCategory()
    {
        $id   = $this->getRequest()->getParam('id', 0);

        $category = $this->categoryFactory->create();
        if ($id) {
            $category->load($id);
        }

        $this->registry->register('current_logo_category', $category);

        return $category;
    }

    /**
     * Saves a font
     *
     * @param LogoCategory $category
     */
    protected function saveCategory(LogoCategory $category)
    {
        $name = $this->getRequest()->getParam('name', '');
        $parentId = $this->getRequest()->getParam('parent_category_id', 0);

        if (empty($name)) {
            return ;
        }

        $category
            ->setName($name)
            ->setParentCategoryId($parentId);

        try {
            $category->save();

            $this->messageManager->addSuccessMessage(__('Category has been saved.'));
            $redirectUrl = $this->getUrl('*/*/index');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $redirectUrl = $this->getUrl('*/*/*');
        }

        $this->getResponse()->setRedirect($redirectUrl);
    }

}
