<?php

namespace Bison\CustomizableProducts\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class LogoCategory extends AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('customizer_logo_category', 'category_id');
    }


}

