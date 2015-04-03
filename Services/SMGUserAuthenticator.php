<?php
namespace Webridge\Oauth2AccessBundle\Services;

use MVMS\ApiBundle\Repository\MobileAppUserRepository;
use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class SMGUserAuthenticator extends AbstractSMGUserAuthenticator
{
    public function __construct(SMGUserProxy $smgUserProxy)
    {
        $this->smgUserProxy = $smgUserProxy;
    }

    public function authenticateToken(
        TokenInterface $token,
        UserProviderInterface $userProvider,
        $providerKey
    ) {
        $authorizationHeader = $token->getCredentials();
        $user = null;
        if ($userProvider instanceof MobileAppUserRepository) {
            $smgUserData = $this->smgUserProxy->validateBearerToken(
                $authorizationHeader
            );
            $user = $userProvider->loadFromSmgUserData($smgUserData);
        }

        if (!$user) {
            throw new AuthenticationException(
                sprintf('Token validation failed for "%s"', $authorizationHeader)
            );
        }

        return new PreAuthenticatedToken(
            $user,
            $authorizationHeader,
            $providerKey,
            $user->getRoles()
        );
    }
}
