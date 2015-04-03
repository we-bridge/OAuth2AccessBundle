<?php
namespace MVMS\ApiBundle\Test\Security;

use Webridge\Oauth2AccessBundle\Services\SMGUserProxy;
use GuzzleHttp\Subscriber\Mock;
use GuzzleHttp\Subscriber\History;
use GuzzleHttp\Message\Response;

/**
 * @group auth
 */
class SMGUserProxyTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->guzzleResponder = new Mock();
        $this->userProxy = SMGUserProxy::createForBaseUrl('http://somewhere/');
        $this->userProxy->attachListener($this->guzzleResponder);
    }

    public function testValidateBearerToken()
    {
        $sampleToken = 'M2I3OWYxYmIwYTk5NjRlZWE2YzQzN2Q5YmZiY2IzYzcxMzBhYzMwNmZmMTE2MDE1MmYyNWM5MmM3NjVmMWEwOQ';
        $credentials = "Bearer $sampleToken";
        $this->guzzleResponder->addResponse(__DIR__.'/smg_oauth2_200_response.txt');
        $guzzleHistory = new History();
        $this->userProxy->attachListener($guzzleHistory);

        $userJson = $this->userProxy->validateBearerToken($credentials);

        $lookupUrl = $guzzleHistory->getLastRequest()->getUrl();
        $this->assertStringEndsWith("/$sampleToken", $lookupUrl);
        $this->assertNotNull($userJson);
        $this->assertArrayHasKey('username', $userJson);
        $this->assertArrayHasKey('id', $userJson);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testValidateBearerTokenHandlesError()
    {
        $this->guzzleResponder->addResponse(new Response(404));
        $this->userProxy->validateBearerToken("Not a valid token at all");
    }
}
