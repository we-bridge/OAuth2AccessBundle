<?php
namespace Webridge\Oauth2AccessBundle\Services;

use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

abstract class AbstractSMGUserAuthenticator implements SimplePreAuthenticatorInterface
{
    public function createToken(Request $request, $providerKey)
    {
        $authorizationHeader = $request->headers->get('Authorization');

        if (!$authorizationHeader) {
            throw new BadCredentialsException('No Authorization header found');
        }

        return new PreAuthenticatedToken(
            'anonymous',
            $authorizationHeader,
            $providerKey
        );
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken
            && $token->getProviderKey() === $providerKey;
    }

}
