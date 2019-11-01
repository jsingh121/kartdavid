<?php

namespace Bison\SvgFonts\Setup;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * Directory list
     *
     * @var DirectoryList
     */
    protected $directoryList;

    public function __construct(DirectoryList $directoryList)
    {
        $this->directoryList = $directoryList;
    }

    /**
     * Function install
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        try {
            $mediaPath = $this->directoryList->getPath(DirectoryList::MEDIA);
            mkdir($mediaPath.'/svg_fonts/', 0777);
        } catch (\Magento\Framework\Exception\FileSystemException $exception) {
            echo 'media/svg_fonts directory could not been created. Please check parent directory permissions.';
        }
    }
}