<?php

namespace Perform\UserBundle\Security;

use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\HttpKernel\UriSigner;
use Perform\UserBundle\Entity\User;

/**
 * Login through a signed token in the url.
 *
 * The token is only valid once. The user's last login date is updated
 * on login, which will invalidate the signed url.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SingleUseAuthenticator implements SimplePreAuthenticatorInterface
{
    protected $signer;

    public function __construct(UriSigner $signer)
    {
        $this->signer = $signer;
    }

    /**
     * Take a target url and add query params to enable a single use login.
     *
     * @param User   $user
     * @param strign $targetUrl
     *
     * @return string
     */
    public function generateUrl(User $user, $targetUrl)
    {
        $pieces = parse_url($targetUrl);
        $hasQuery = isset($pieces['query']);
        $timestamp = $user->getLastLogin() ? (string) $user->getLastLogin()->getTimestamp() : '';
        $urlWithParams = $targetUrl.($hasQuery ? '&' : '?').sprintf('_a=%s&_t=%s', base64_encode($user->getEmail()), base64_encode($timestamp));

        return $this->signer->sign($urlWithParams);
    }

    public function createToken(Request $request, $providerKey)
    {
        if (!$request->query->has('_a')) {
            return;
        }
        if (!$this->signer->check($request->getUri())) {
            return;
        }
        $email = base64_decode($request->query->get('_a'));
        $lastLogin = base64_decode($request->query->get('_t', ''));

        return new PreAuthenticatedToken($email, $lastLogin, $providerKey);
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $user = $userProvider->loadUserByUsername($token->getUser());
        if (!$user) {
            return;
        }
        if (strlen($token->getCredentials() > 0)) {
            // last login timestamp included, check it is the same as the user's last login
            if ($token->getCredentials() !== (string) $user->getLastLogin()->getTimestamp()) {
                return;
            }
        } else {
            // last login timestamp not included, must be the first login
            if ($user->getLastLogin() instanceof \DateTime) {
                return;
            }
        }

        return new PreAuthenticatedToken($user, $token->getCredentials(), $providerKey, $user->getRoles());
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }
}
