<?php
namespace Webridge\Oauth2AccessBundle\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use GuzzleHttp\Command\Guzzle\Description;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SMGUserProxy
{
    const URL_PATH = 'oauth/access_token_valid/';

    private $serviceUrl;

    public static function createForBaseUrl($baseUrl)
    {
        $serviceUrl = $baseUrl . self::URL_PATH;
        return new self($serviceUrl);
    }

    private function __construct($serviceUrl)
    {
        if (empty($serviceUrl)) {
            throw new \RuntimeException('$serviceUrl not set');
        }

        $this->serviceUrl = $serviceUrl;

        $this->serviceDescription = new Description([
            'baseUrl' => $this->serviceUrl,
            'operations' => [
                'access_token_valid' => [
                    'httpMethod' => 'GET',
                    'uri' => $this->serviceUrl.'{token}',
                    'responseModel' => 'getResponse',
                    'parameters' => [
                        'token' => [
                            'type' => 'string',
                            'location' => 'uri'
                        ]
                    ]
                ]
            ],
            'models' => [
                'getResponse' => [
                    'type' => 'object',
                    'additionalProperties' => [
                        'location' => 'json'
                    ]
                ]
            ]
        ]);
        $this->httpClient = new Client();
    }

    public function attachListener($listener)
    {
        $this->httpClient->getEmitter()->attach($listener);
    }

    public function validateBearerToken($credentials)
    {
        try {
            return $this->requestUserinfoFromOauthService($credentials);
        } catch (\GuzzleHttp\Command\Exception\CommandClientException $e) {
            throw new AccessDeniedException("Token validation failed", $e);
        }
    }

    private function requestUserinfoFromOauthService($credentials)
    {
        $bearerToken = $this->extractBearerToken($credentials);
        $this->guzzleClient = new GuzzleClient($this->httpClient, $this->serviceDescription);
        return $this->guzzleClient->access_token_valid(['token'=>$bearerToken]);
    }

    private function extractBearerToken($credentials)
    {
        $parts = explode('Bearer ', $credentials);
        return (isset($parts[1]) ? $parts[1] : '');
    }
}
