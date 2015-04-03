<?php
namespace Webridge\Oauth2AccessBundle\Tests\Services;

use Webridge\Oauth2AccessBundle\Services\SMGUserAuthenticator;
use Webridge\Oauth2AccessBundle\User\Oauth2AccessUserInterface;

/**
 * @group auth
 */
class SMGUserAuthenticatorTest extends \PHPUnit_Framework_TestCase
{
    const USER = 'user1';
    const USER_ROLE = 'ROLE_MOBILE_APP_USER';
    const PROVIDER_KEY = 'smg_user_authenticator';
    const AUTHZ_HEADER_VALUE = 'Bearer ICAgICAgICAgICAgICAgICBQRUVLQUJPTyAgICAgICAgICAgICAgICAgCg==';

    public function setUp()
    {
        $this->authenticator = new SMGUserAuthenticator(
            $this->buildMockSMGUserProxy()
        );

        $this->fakeRequest = $this->buildMockRequest();

        $this->providerToken = $this->authenticator->createToken(
            $this->fakeRequest,
            $this::PROVIDER_KEY
        );
    }

    public function testCreateToken()
    {
        $this->assertInstanceOf(
            'Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken',
            $this->providerToken
        );
        $this->assertEquals('anonymous', $this->providerToken->getUsername());
        $this->assertEquals(
            $this::AUTHZ_HEADER_VALUE,
            $this->providerToken->getCredentials()
        );
        $this->assertEmpty(
            $this->providerToken->getRoles(),
            'anonymous should NOT have any Role'
        );
    }

    public function testSupportsToken()
    {
        $this->assertFalse(
            $this->authenticator->supportsToken(
                $this->providerToken,
                'different provider'
            )
        );
        $this->assertTrue(
            $this->authenticator->supportsToken(
                $this->providerToken,
                $this::PROVIDER_KEY
            )
        );
    }

    public function testAuthenticateToken()
    {
        $fakeUserProvider = $this->buildMockUserProvider();

        $authenticatedToken = $this->authenticator->authenticateToken(
            $this->providerToken,
            $fakeUserProvider,
            $this::PROVIDER_KEY
        );

        $this->assertInstanceOf(
            'MVMS\ApiBundle\Entity\MobileAppUser',
            $authenticatedToken->getUser()
        );
        $this->assertEquals(
            $this::USER,
            $authenticatedToken->getUsername()
        );

        $roles = $authenticatedToken->getRoles();
        $this->assertCount(1, $roles);

        $firstRole = $roles[0]->getRole();
        $this->assertEquals(
            $this::USER_ROLE,
            $firstRole,
            'user1 should have user role'
        );
    }

    private function buildMockUserProvider()
    {
        $mock = $this->getMockBuilder('Webridge\Oauth2AccessBundle\Services\Oauth2AccessUserProviderInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $mock
            ->expects($this->once())
            ->method('loadFromSmgUserData')
            ->willReturn($user);
        return $mock;
    }

    private function buildMockUser()
    {
        $mock = $this->getMock('MVMS\ApiBundle\Entity\MobileAppUser');
        $mock->method('getRoles')->willReturn([$this::USER_ROLE]);
        return $mock;
    }

    private function buildMockRequest()
    {
        $mock = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $mock->headers = new \Symfony\Component\HttpFoundation\HeaderBag([
            'Authorization' => $this::AUTHZ_HEADER_VALUE
        ]);
        return $mock;
    }

    private function buildMockSMGUserProxy()
    {
        $mock = $this->getMockBuilder('Webridge\Oauth2AccessBundle\Services\SMGUserProxy')
            ->disableOriginalConstructor()
            ->getMock();
        $mock->method('validateBearerToken')->willReturn([
            'username'=>$this::USER,
            'email'=>'user1@dev.null',
            'id'=>1234
        ]);
        return $mock;
    }

}
