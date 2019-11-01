<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Model\Config\Backend\TrustSeals;

use Aheadworks\OneStepCheckout\Model\Config\Backend\TrustSeals\Badges\Validator;
use Aheadworks\OneStepCheckout\Model\Config\Backend\ConfigValue;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * Class Badges
 * @package Aheadworks\OneStepCheckout\Model\Config\Backend\TrustSeals
 */
class Badges extends ConfigValue
{
    /**
     * @var Validator
     */
    private $validator;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param Validator $validator
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        Validator $validator,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave()
    {
        $value = $this->resolveSerializedValue();
        unset($value['__empty']);
        $value = array_values($value);
        $this->setValue(serialize($value));
        return $this;
    }

    /**
     * Process data after load
     *
     * @return $this
     */
    public function afterLoad()
    {
        $value = unserialize($this->getValue());
        if (is_array($value)) {
            $value = $this->prepareValue($value);
            $this->setValue($value);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _getValidationRulesBeforeSave()
    {
        return $this->validator;
    }

    /**
     * Prepare value before saving
     * Escape quotes
     *
     * @param array $value
     * @return array
     */
    private function prepareValue($value)
    {
        foreach ($value as &$badgeData) {
            if (is_array($badgeData) && array_key_exists('script', $badgeData)) {
                $badgeData['script'] = str_ireplace("'", "&#039;", $badgeData['script']);
            }
        }

        return $value;
    }
}
