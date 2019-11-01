<?php

namespace Bison\CustomizableProducts\Model;

use \Magento\Framework\Model\AbstractModel;

/**
 * Predefined logo' category class
 *
 * @method LogoCategory setName(string)
 * @method string getName()
 * @method LogoCategory setParentCategoryId(int)
 * @method int getParentCategoryId()
 *
 * @package Bison\CustomizableProducts\Model
 */
class LogoCategory extends AbstractModel
{
    /**
     * Initialize resource model
     * @return void
     */
    public function _construct()
    {
        $this->_init(ResourceModel\LogoCategory::class);
    }

}

