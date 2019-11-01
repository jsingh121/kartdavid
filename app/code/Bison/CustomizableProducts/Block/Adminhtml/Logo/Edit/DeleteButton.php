<?php

namespace Bison\CustomizableProducts\Block\Adminhtml\Logo\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends Generic implements ButtonProviderInterface
{
    /**
     * Return button data
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Delete'),
            'class' => 'delete',
            'on_click' => 'deleteConfirm(\''
                . __('Are you sure you want to delete this font?')
                . '\', \'' . $this->getDeleteUrl() . '\')',
            'sort_order' => 20,
        ];
    }

    /**
     * Get URL for delete button
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['id' => $this->getId()]);
    }
    
}
