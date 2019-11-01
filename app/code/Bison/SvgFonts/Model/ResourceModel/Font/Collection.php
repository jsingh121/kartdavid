<?php

namespace Bison\SvgFonts\Model\ResourceModel\Font;

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
        $this->_init(\Bison\SvgFonts\Model\Font::class, \Bison\SvgFonts\Model\ResourceModel\Font::class);
    }
}
