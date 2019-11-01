<?php

namespace Bison\SvgFonts\Model\ResourceModel\Font;

use Bison\SvgFonts\Model\Font;

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
     * @return Collection|Font[]
     */
    public function create(array $data = [])
    {
        return $this->objectManager->create($this->instanceName, $data);
    }

}
