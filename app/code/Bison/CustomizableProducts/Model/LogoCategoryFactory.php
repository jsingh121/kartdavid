<?php

namespace Bison\CustomizableProducts\Model;

use Magento\Framework\ObjectManagerInterface;

/**
 * Factory class for Bison\CustomizableProducts\Model\LogoCategory
 * @api
 * @since 100.0.2
 */
class LogoCategoryFactory
{
    /**
     * Object Manager instance
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $instanceName;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        $instanceName = LogoCategory::class
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Bison\CustomizableProducts\Model\LogoCategory
     */
    public function create(array $data = [])
    {
        return $this->objectManager->create($this->instanceName, $data);
    }
}
