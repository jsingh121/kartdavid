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
class RemoveBackground extends Action
{
    const API_URL = 'https://clippingmagic.com/';

    const IMAGE_SIZE_ERROR_CODE = 1013;

    /**
     * Json de/encoder
     *
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;

    /**
     * Response body
     *
     * @var responseBody
     */
    protected $responseBody;

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

        $apiClient = new Client([
            'base_uri' => self::API_URL,
            'exceptions' => false,
            'timeout' => 2.0
        ]);

        try{
            $logo = $this->loadLogo();

            $data = [
                'auth' => [
                    $this->configProvider->getApiId(),
                    $this->configProvider->getApiKey()
                ],
                'timeout' => 15,
                'multipart' => [
                    [
                        'name' => 'image',
                        'contents' => fopen($logo->getLogoFilePath(), 'r'),
                        'headers' => ['Content-Type' => $logo->getImageContentType()]
                    ],
                ]
            ];

            if ($this->configProvider->isTestMode()) {
                $data['multipart'][] = [
                    'name' => 'test',
                    'contents' => 'true',
                    'headers' => ['Content-Type', 'application/json']
                ];
            }

            $response = $apiClient->post('/api/v1/images', $data);

            $statusCode = $response->getStatusCode();

            if ($statusCode >= 400 && $statusCode < 500) {
                $this->responseBody = $response->getBody()->getContents();
            }

            $imageData = $this->json->unserialize($response->getBody()->getContents());

            $result->setData(
                [
                    'success' => true,
                    'imageId' => $imageData['image']['id'],
                    'secret' => $imageData['image']['secret']
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
            $errorMessage = __('An error occurred. Please try again.');
            $responseBody = json_decode($this->responseBody);

            if ($responseBody->error->code === self::IMAGE_SIZE_ERROR_CODE) {
                $errorMessage = $responseBody->error->message;
            }

            $result->setData(
                [
                    'success' => false,
                    'error' => $errorMessage
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
            $logoId = $this->getRequest()->getParam('logo_id', 0);

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
