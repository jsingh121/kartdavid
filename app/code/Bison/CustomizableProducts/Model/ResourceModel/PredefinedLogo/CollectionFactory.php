<?php

namespace Bison\CustomizableProducts\Model\ResourceModel\PredefinedLogo;

use Bison\CustomizableProducts\Model\LogoCategory;
use Bison\CustomizableProducts\Model\ResourceModel\PredefinedLogo;

/**
 * Class CollectionFactory
 *
 * @method PredefinedLogo setName(string)
 * @method string getName()
 * @package Bison\CustomizableProducts\Model\ResourceModel\PredefinedLogo
 */
class CollectionFactory implements \Magento\Reports\Model\ResourceModel\Quote\CollectionFactoryInterface
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $instanceName = null;

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = Collection::class)
    {
        $this->objectManager = $objectManager;
        $this->instanceName  = $instanceName;
    }

    /**
     * Creates collection
     *
     * @param array $data
     *
     * @return Collection|PredefinedLogo[]
     */
    public function create(array $data = [])
    {
        return $this->objectManager->create($this->instanceName, $data);
    }

}
