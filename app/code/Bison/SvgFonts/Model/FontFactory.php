<?php

namespace Bison\SvgFonts\Model;

/**
 * Factory class for Font class
 *
 * @see Font
 */
class FontFactory
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
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = Font::class)
    {
        $this->objectManager = $objectManager;
        $this->instanceName  = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     *
     * @return Font
     */
    public function create(array $data = array())
    {
        return $this->objectManager->create($this->instanceName, $data);
    }

}
