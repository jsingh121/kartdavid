<?php

namespace Bison\CustomizableProducts\Model\ResourceModel\Inspiration;

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
        $this->_init(\Bison\CustomizableProducts\Model\Inspiration::class, \Bison\CustomizableProducts\Model\ResourceModel\Inspiration::class);
    }
}
