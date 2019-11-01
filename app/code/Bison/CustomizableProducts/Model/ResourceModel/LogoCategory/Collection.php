<?php

namespace Bison\CustomizableProducts\Model\ResourceModel\LogoCategory;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(\Bison\CustomizableProducts\Model\LogoCategory::class, \Bison\CustomizableProducts\Model\ResourceModel\LogoCategory::class);
    }
}
