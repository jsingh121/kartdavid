<?php

namespace Bison\CustomizableProducts\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;

/**
 * Class Cookie
 * @package Bison\CustomizableProducts\Helper
 */
class Cookie extends AbstractHelper
{
    const DESIGN_COOKIE_NAME = 'saved_designs';

    /** @var CookieManagerInterface */
    protected $cookieManager;

    /** @var CookieMetadataFactory */
    protected $cookieMetadataFactory;

    /** @var SessionManagerInterface */
    protected $sessionManager;

    /** @var RemoteAddress */
    protected $remoteAddress;

    /**
     * Cookie constructor.
     *
     * @param Context $context
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param SessionManagerInterface $sessionManager
     * @param RemoteAddress $remoteAddress
     */
    public function __construct(
        Context $context,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        SessionManagerInterface $sessionManager,
        RemoteAddress $remoteAddress

    )
    {
        parent::__construct($context);
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sessionManager = $sessionManager;
        $this->remoteAddress = $remoteAddress;
    }

    /**
     * Get cookie by name
     * @return null|string
     */
    public function get()
    {
        return $this->cookieManager->getCookie(self::DESIGN_COOKIE_NAME);
    }

    /**
     * Set data to cookie in remote address
     *
     * @param string $value
     * @param int $duration
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function set(string $value, $duration = 604800)
    {
        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDuration($duration)
            ->setPath($this->sessionManager->getCookiePath())
            ->setDomain($this->sessionManager->getCookieDomain());

        $this->cookieManager->setPublicCookie(
            self::DESIGN_COOKIE_NAME,
            $value,
            $metadata
        );
    }

    /**
     * Delete cookie remote address
     *
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function delete()
    {
        $this->cookieManager->deleteCookie(
            self::DESIGN_COOKIE_NAME,
            $this->cookieMetadataFactory
                ->createCookieMetadata()
                ->setPath($this->sessionManager->getCookiePath())
                ->setDomain($this->sessionManager->getCookieDomain())
        );
    }
}