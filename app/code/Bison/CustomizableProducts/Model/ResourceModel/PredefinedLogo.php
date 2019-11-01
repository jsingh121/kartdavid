<?php

namespace Bison\CustomizableProducts\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class PredefinedLogo extends AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('customizer_predefined_logo', 'logo_id');
    }
}

