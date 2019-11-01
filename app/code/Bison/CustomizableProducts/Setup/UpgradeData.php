<?php

namespace Bison\CustomizableProducts\Setup;

use Magento\Eav\Setup\EavSetupFactory;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Quote\Setup\QuoteSetupFactory;

class UpgradeData implements UpgradeDataInterface
{
    const INSPIRATIONS_GALLERY_CODE = 'inspirations_gallery';

    private $eavSetupFactory;

    private $directoryList;

    /**
     * Quote setup factory
     *
     * @var QuoteSetupFactory
     */
    private $quoteSetupFactory;

    /**
     * Sales Order setup factory

     * @var SalesSetup
     */
    private $salesSetupFactory;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory,
        DirectoryList $directoryList
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->directoryList = $directoryList;
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.2.0') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('catalog_product_option'),
                'layer_id',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 10,
                    'nullable' => true,
                    'comment' => 'Layer Id'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('catalog_product_option_type_value'),
                'is_fluorescent',
                [
                    'type' => Table::TYPE_BOOLEAN,
                    'length' => 10,
                    'nullable' => true,
                    'comment' => 'Is Fluorescent'
                ]
            );

        }

        if (version_compare($context->getVersion(), '1.3.0') < 0) {
            try {
                $mediaPath = $this->directoryList->getPath(DirectoryList::MEDIA);
                mkdir($mediaPath . '/user_logo/', 0777);
            } catch (\Magento\Framework\Exception\FileSystemException $exception) {
                echo 'media/user-logo directory could not been created. Please check parent directory permissions.';
            }
        }


        if (version_compare($context->getVersion(), '1.4.0') < 0) {
            try {
                $mediaPath = $this->directoryList->getPath(DirectoryList::MEDIA);
                mkdir($mediaPath . '/inspirations/', 0777);
            } catch (\Magento\Framework\Exception\FileSystemException $exception) {
                echo 'media/user-logo directory could not been created. Please check parent directory permissions.';
            }
        }

        if (version_compare($context->getVersion(), '1.4.0') < 0) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'bodywork',
                [
                    'group' => 'General',
                    'backend' => \Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend::class,
                    'label' => 'Bodywork',
                    'type' => 'int',
                    'input' => 'select',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'visible_on_front' => true,
                    'used_in_product_listing' => true,
                    'is_used_in_grid' => true,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => true,
                    'apply_to' => 'simple,virtual,configurable'
                ]
            );

            /** @var QuoteSetup $quoteSetup */
            $quoteSetup = $this->quoteSetupFactory->create(['setup' => $setup]);

            /** @var SalesSetup $salesSetup */
            $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);

            $orderItemAttributeOptions = [
                'type'     => Table::TYPE_TEXT,
                'visible'  => true,
                'required' => false
            ];
            $quoteSetup->addAttribute('quote_item', 'generated_svg', $orderItemAttributeOptions);
            $salesSetup->addAttribute('order_item', 'generated_svg', $orderItemAttributeOptions);

        }

        if (version_compare($context->getVersion(), '1.4.5') < 0) {
            try {
                $mediaPath = $this->directoryList->getPath(DirectoryList::MEDIA);
                mkdir($mediaPath.'/quote-svg/', 0777);
            } catch (\Magento\Framework\Exception\FileSystemException $exception) {
                echo 'media/quote-svg directory could not been created. Please check parent directory permissions.';
            }
        }

        if (version_compare($context->getVersion(), '1.5.0') < 0) {
            try {
                $mediaPath = $this->directoryList->getPath(DirectoryList::MEDIA);
                mkdir($mediaPath.'/user_design/', 0777);
            } catch (\Magento\Framework\Exception\FileSystemException $exception) {
                echo 'media/user-design directory could not been created. Please check parent directory permissions.';
            }

            $setup->getConnection()->addColumn(
                $setup->getTable('catalog_product_option_type_value'),
                'color_name',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 100,
                    'nullable' => true,
                    'comment' => 'Color Name'
                ]
            );

        }

        if (version_compare($context->getVersion(), '1.6.0') < 0) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'logos_category',
                [
                    'group' => 'General',
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Logos category',
                    'input' => 'select',
                    'class' => '',
                    'source' => \Bison\CustomizableProducts\Model\Source\LogoAttribute::class,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => '0',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => true,
                    'used_in_product_listing' => true,
                    'unique' => false,

                ]
            );


            try {
                $mediaPath = $this->directoryList->getPath(DirectoryList::MEDIA);
                mkdir($mediaPath.'/predefined-logo/', 0777);
            } catch (\Magento\Framework\Exception\FileSystemException $exception) {
                echo 'media/quote-svg directory could not been created. Please check parent directory permissions.';
            }
        }
        if (version_compare($context->getVersion(), '1.6.5') < 0) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'name_maximum_length',
                [
                    'group' => 'General',
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Name maximum length',
                    'input' => 'text',
                    'class' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => '0',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => true,
                    'used_in_product_listing' => true,
                    'unique' => false,

                ]
            );
        }


        $setup->endSetup();
    }
}