<?php

namespace Bison\CustomizableProducts\Model\ResourceModel\Logo;

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
        $this->_init(\Bison\CustomizableProducts\Model\Logo::class, \Bison\CustomizableProducts\Model\ResourceModel\Logo::class);
    }
}
