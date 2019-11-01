<?php

namespace Dolphin\Freesample\Block\Adminhtml\Grid\Edit;

/**
 * Adminhtml Add New Row Form.
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    
    protected $_systemStore;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Dolphin\Freesample\Model\Status $options,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_options = $options;
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $model = $this->_coreRegistry->registry('freesample');
        $form = $this->_formFactory->create(
            ['data' => [
                            'id' => 'edit_form', 
                            'enctype' => 'multipart/form-data', 
                            'action' => $this->getData('action'), 
                            'method' => 'post',
                        ]
            ]
        );

        if ($this->_isAllowedAction('Dolphin_Freesample::ingres_fetch_object(result)')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $form->setHtmlIdPrefix('freesample_');
        
        $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Edit Sample Order'), 'class' => 'fieldset-wide']
            );
            
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
        
        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'class' => 'required-entry',
                'required' => true,
                
            ]
        );

        $fieldset->addField(
            'email',
            'text',
            [
                'name' => 'email',
                'label' => __('Email'),
                'title' => __('Email'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'street',
            'text',
            [
                'name' => 'street',
                'label' => __('Street'),
                'title' => __('Street'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'city',
            'text',
            [
                'name' => 'city',
                'label' => __('City'),
                'title' => __('City'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'postal_code',
            'text',
            [
                'name' => 'postal_code',
                'label' => __('Postal Code'),
                'title' => __('Postal Code'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'country',
            'text',
            [
                'name' => 'country',
                'label' => __('Country'),
                'title' => __('Country'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'created_at',
            'date',
            [
                'name' => 'created_at',
                'label' => __('Created At'),
                'date_format' => $dateFormat,
                'time_format' => 'H:mm:ss',
                'class' => 'validate-date validate-date-range date-range-custom_theme-from',
                'class' => 'required-entry',
                'style' => 'width:200px',
            ]
        );
        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'values' => $this->_options->getOptionArray(),
                'class' => 'status',
                'required' => true,
            ]
        );
    
        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();        
    }

    public function _isAllowedAction($resourceId)
        {
            return $this->_authorization->isAllowed($resourceId);
        }

}
