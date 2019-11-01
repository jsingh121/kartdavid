<?php

namespace Bison\CustomizableProducts\Controller\Logo;

use Bison\CustomizableProducts\Model\ConfigProvider;
use Bison\CustomizableProducts\Model\Logo;
use Bison\CustomizableProducts\Model\LogoFactory;
use GuzzleHttp\Client;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class RemoveBackground
 *
 * @package Bison\CustomizableProducts\Controller\Logo
 */
class Update extends Action
{
    /**
     * Json de/encoder
     *
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;

    /**
     * Logo factory
     *
     * @var LogoFactory
     */
    protected $logoFactory;

    /**
     * Config provider
     *
     * @var ConfigProvider
     */
    protected $configProvider;

    public function __construct(
        Context $context,
        \Magento\Framework\Serialize\Serializer\Json $json,
        LogoFactory $logoFactory,
        ConfigProvider $configProvider
    ) {
        parent::__construct($context);
        $this->json = $json;
        $this->logoFactory = $logoFactory;
        $this->configProvider = $configProvider;
    }

    /**
     * Returns given product bodywork svg
     *
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var Json $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $imageId = $this->getRequest()->getParam('imageId', 0);

        $apiClient = new Client([
            'base_uri' => 'https://clippingmagic.com/',
            'timeout' => 0
        ]);

        try{
            $logo = $this->loadLogo();

            $response = $apiClient->get(
                '/api/v1/images/'.$imageId,
                [
                    'auth' => [
                        $this->configProvider->getApiId(),
                        $this->configProvider->getApiKey()
                    ]
                ]
            );
            $image = $response->getBody()->getContents();

            file_put_contents($logo->getLogoFilePath(), $image);

            $fileName = $logo->getLogoName();
            $newLogoFileName = str_replace(['jpg', 'jpeg', 'gif', 'svg'], 'png', $fileName);
            rename($logo->getLogoFilePath(), str_replace($fileName, $newLogoFileName, $logo->getLogoFilePath()));
            $logo->setLogoName($newLogoFileName)->save();

            $result->setData(
                [
                    'success' => true,
                    'url' => $logo->getLogoFileUrl()
                ]
            );
        } catch (LocalizedException $e) {
            $result->setData(
                [
                    'success' => false,
                    'error' => $e->getMessage()
                ]
            );
        } catch (\Exception $e) {
            $result->setData(
                [
                    'success' => false,
                    'error' => __('An error occurred. Please try again.')
                ]
            );
        }

        return $result;
    }

    /**
     * Loads logo from request
     *
     * @return Logo
     *
     * @throws LocalizedException
     */
    protected function loadLogo() {
        try {
            $logoId = $this->getRequest()->getParam('logoId', 0);

            if (!$logoId) {
                throw new \Exception('');
            }
            /** @var Logo $logo */
            $logo = $this->logoFactory->create();
            $logo->load($logoId);
            return $logo;
        } catch (\Exception $exception) {
            throw new LocalizedException(__('Selected logo does not exist.'));
        }
    }

}
