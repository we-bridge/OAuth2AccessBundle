<?php
namespace Webridge\Oauth2AccessBundle\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use GuzzleHttp\Command\Guzzle\Description;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\DependencyInjection\ContainerAware;

class SMGUserProxy extends ContainerAware
{
    private $baseUrl;

    public static function configureRemoteEndpoints()
    {
        $this->httpClient = new Client();
        $this->baseUrl = $this->container->getParameter('webridge_oauth2_access.upstream_base_url');

        $this->upstreamOauth2Service = $this->buildServiceDescription();
    }

    public function buildServiceDescription()
    {
        return new Description([
            'baseUrl' => $this->baseUrl,
            'operations' => [
                'access_token_valid' => [
                    'httpMethod' => 'GET',
                    'uri' => 'oauth/access_token_valid/{token}',
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
        $this->guzzleClient = new GuzzleClient($this->httpClient, $this->upstreamOauth2Service);
        return $this->guzzleClient->access_token_valid(['token'=>$bearerToken]);
    }

    private function extractBearerToken($credentials)
    {
        $parts = explode('Bearer ', $credentials);
        return (isset($parts[1]) ? $parts[1] : '');
    }
}
