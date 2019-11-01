<?php

namespace Bison\CustomizableProducts\Observer;

use Magento\Catalog\Model\Product;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Api\ProductCustomOptionRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Bison\CustomizableProducts\Model\Inspiration;
use Bison\CustomizableProducts\Model\InspirationFactory;

/**
 * Class ProductAfterSave
 * @package Bison\CustomizableProducts\Observer
 */
class ProductAfterSave implements ObserverInterface
{
    const OPTION_VALUE_TO_BE_SAVED = 'is_fluorescent';

    /** @var ProductCustomOptionRepositoryInterface */
    private $customOptionRepository;

    /** @var RequestInterface */
    private $request;

    /** @var Inspiration */
    private $inspiration;

    /** @var InspirationFactory */
    private $inspirationFactory;

    /**
     * ProductAfterSave constructor.
     * @param ProductCustomOptionRepositoryInterface $customOptionRepository
     * @param RequestInterface $request
     * @param Inspiration $inspiration
     * @param InspirationFactory $inspirationFactory
     */
    public function __construct(
        ProductCustomOptionRepositoryInterface $customOptionRepository,
        RequestInterface $request,
        Inspiration $inspiration,
        InspirationFactory $inspirationFactory
    )
    {
        $this->customOptionRepository = $customOptionRepository;
        $this->request = $request;
        $this->inspiration = $inspiration;
        $this->inspirationFactory = $inspirationFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getProduct();
        $this->updateCustomOptions($product);
        $ids = $this->saveInspiration($product);
        $this->removeUnusedInspirations($ids);
    }

    /**
     * Update Custom option
     * @param \Magento\Catalog\Model\Product $product
     * @throws \Exception
     */
    private function updateCustomOptions(\Magento\Catalog\Model\Product $product)
    {
        if ($product->getOrigData('options')) {
            foreach ($product->getOrigData('options') as $option) {
                if (!$option->getValues()) {
                    continue;
                }

                foreach ($option->getValues() as $origValue) {
                    $data = $this->getChangedData($origValue);
                    if (!empty($data)) {
                        $this->updateOptionValue($origValue, $data);
                    }
                }
            }
        }
    }

    /**
     * Check if data has changed and return it.
     * @param \Magento\Catalog\Model\Product\Option\Value $origValue
     * @return array
     */
    private function getChangedData(\Magento\Catalog\Model\Product\Option\Value $origValue)
    {
        $changedData = [];
        $postValues = $this->getPostProductOptionsValues();
        if (empty($postValues)) {
            return $changedData;
        }

        foreach ($postValues as $postValue) {
            if (isset($postValue['option_type_id']) && $origValue->getData('option_type_id') === $postValue['option_type_id'] &&
                $origValue->getData(self::OPTION_VALUE_TO_BE_SAVED) != $postValue[self::OPTION_VALUE_TO_BE_SAVED]) {
                $changedData = [
                    self::OPTION_VALUE_TO_BE_SAVED => $postValue[self::OPTION_VALUE_TO_BE_SAVED],
                    'option_type_id' => $postValue['option_type_id']
                ];

            }
        }

        return $changedData;
    }

    /**
     * Update option value
     * @param \Magento\Catalog\Model\Product\Option\Value $origValue
     * @param array $data
     * @throws \Exception
     */
    private function updateOptionValue(\Magento\Catalog\Model\Product\Option\Value $origValue, array $data)
    {
        $origValue->setData(self::OPTION_VALUE_TO_BE_SAVED, $data[self::OPTION_VALUE_TO_BE_SAVED]);
        $origValue->save();
    }

    /**
     * Validate and return post product options values.
     * @return array
     */
    private function getPostProductOptionsValues()
    {
        $postOptionValues = [];
        $postProduct = $this->request->getParam('product');
        if (!$postProduct) {
            return $postOptionValues;
        }

        foreach ($postProduct['options'] as $option) {
            if (!isset($option['values'])) {
                continue;
            }

            foreach ($option['values'] as $postValue) {
                if (!isset($postValue['is_fluorescent'])) {
                    continue;
                }

                $postOptionValues[] = $postValue;
            }
        }

        return $postOptionValues;
    }

    /**
     * Save inspiration model
     * @param Product $product
     * @return array
     * @throws \Exception
     */
    private function saveInspiration(Product $product)
    {
        $ids = [];
        $inspirations = $product->getInspiration();

        if (!$inspirations) {
            return $ids;
        }

        foreach ($inspirations as $inspiration) {
            if (isset($inspiration['file'][0]['status']) && $inspiration['file'][0]['status'] === 'old') {
                $ids[] = $inspiration['file'][0]['id'];
                continue;
            }

            $this->inspiration
                ->setName($inspiration['file'][0]['file'])
                ->setProductId($product->getId());

            $this->inspiration->save();
            $ids[] = $this->inspiration->getId();
            $this->inspiration->unsetData();
        }

        return $ids;
    }

    /**
     * @param $ids
     * @throws \Exception
     */
    public function removeUnusedInspirations($ids)
    {
        $inspiration = $this->inspirationFactory->create();
        $inspirationCollection = $inspiration->getCollection();
        if (!empty($ids)) {
            $inspirationCollection->addFieldToFilter('id', ['nin' => $ids]);
        }

        foreach ($inspirationCollection as $inspiration) {
            $inspiration->delete();
        }
    }

}