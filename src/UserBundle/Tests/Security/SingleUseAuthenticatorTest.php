<?php

namespace Perform\UserBundle\Tests\Security;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\UriSigner;
use Perform\UserBundle\Entity\User;
use Perform\UserBundle\Security\SingleUseAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SingleUseAuthenticatorTest extends TestCase
{
    public function setUp()
    {
        // Bit of a hack, use 'z_hash' for the signed query param.
        // UriSigner will put the signed param at the end of the url,
        // but the Request class will parse all query params and sort
        // them in alphabetical order, leading to Request::getUri()
        // returning a different url from the one supplied.
        // For ease of testing, make sure this signed param is always
        // at the end.
        // See testCreateTokenFromValidUrl(), where it uses Request::create().
        $signer = new UriSigner('secret', 'z_hash');
        $this->auth = new SingleUseAuthenticator($signer);
    }

    private function user($email, $lastLogin = null)
    {
        $user = new User();
        $user->setEmail($email);
        if ($lastLogin) {
            $user->setLastLogin(new \DateTime($lastLogin));
        }

        return $user;
    }

    public function generateProvider()
    {
        return [
            [
                $this->user('admin@example.com'),
                'https://example.com/',
                'https://example.com/?_a=YWRtaW5AZXhhbXBsZS5jb20%3D&_t=&z_hash=MJRmPh9wCyMW814V4ENtM9JNhcJmIr%2FFRlMb54Q9SUc%3D',
            ],
            [
                $this->user('admin@example.com', '2016-01-01'),
                'http://example.com/some-page',
                'http://example.com/some-page?_a=YWRtaW5AZXhhbXBsZS5jb20%3D&_t=MTQ1MTYwNjQwMA%3D%3D&z_hash=00JIMJhCnPv0PWEq00Apf%2B4QrU6QFaxaW7F5W50%2B6mk%3D',
            ],
            [
                $this->user('admin@example.com', '2016-01-01'),
                'http://example.com/?foo=bar',
                'http://example.com/?_a=YWRtaW5AZXhhbXBsZS5jb20%3D&_t=MTQ1MTYwNjQwMA%3D%3D&foo=bar&z_hash=te3UHjqgAaQAZCld8xPnI0xpOyWRqe%2Bu8OoKVxas3xE%3D',
            ],
            [
                $this->user('admin@example.com', '2016-01-01'),
                'http://example.com/pages/secret?foo=bar&something=foo',
                'http://example.com/pages/secret?_a=YWRtaW5AZXhhbXBsZS5jb20%3D&_t=MTQ1MTYwNjQwMA%3D%3D&foo=bar&something=foo&z_hash=2HTVkj8sapL%2BpbYJLxzR5wZOQjJLAENRKDt0GPAxP1o%3D',
            ],
        ];
    }

    /**
     * @dataProvider generateProvider
     */
    public function testGenerateUrl(User $user, $originalUrl, $signedUrl)
    {
        $this->assertSame($signedUrl, $this->auth->generateUrl($user, $originalUrl));
    }

    /**
     * @dataProvider generateProvider
     */
    public function testCreateTokenFromValidUrl(User $user, $originalUrl, $signedUrl)
    {
        $token = $this->auth->createToken(Request::create($signedUrl), 'provider');

        $expectedTimestamp = $user->getLastLogin() ? (string) $user->getLastLogin()->getTimestamp() : '';
        $this->assertInstanceOf(PreAuthenticatedToken::class, $token);
        $this->assertSame($user->getEmail(), $token->getUsername());
        $this->assertSame($expectedTimestamp, $token->getCredentials());
    }

    public function testCreateTokenFromInvalidUrl()
    {
        $this->assertNull($this->auth->createToken(Request::create('https://example.com'), 'provider'));
        $this->assertNull($this->auth->createToken(Request::create('https://example.com?_a=fskdj'), 'provider'));
        $this->assertNull($this->auth->createToken(Request::create('https://example.com?_a=fskdj&_t='), 'provider'));
        $this->assertNull($this->auth->createToken(Request::create('https://example.com?_a=fskdj&_t=skdfjnskf'), 'provider'));
        $this->assertNull($this->auth->createToken(Request::create('https://example.com?_a=fskdj&_t=skdfjnskf&z_hash=skdjfnsdkfn'), 'provider'));
    }

    /**
     * @dataProvider generateProvider
     */
    public function testAuthenticateTokenFromValidUrl(User $user, $originalUrl, $signedUrl)
    {
        $token = $this->auth->createToken(Request::create($signedUrl), 'provider');

        $provider = $this->createMock(UserProviderInterface::class);
        $provider->expects($this->any())
            ->method('loadUserByUsername')
            ->with($user->getEmail())
            ->will($this->returnValue($user));

        $authToken = $this->auth->authenticateToken($token, $provider, 'provider');
        $this->assertSame($user, $authToken->getUser());
    }

    /**
     * @dataProvider generateProvider
     */
    public function testAuthenticateFailsWithChangedLastLogin(User $user, $originalUrl, $signedUrl)
    {
        $token = $this->auth->createToken(Request::create($signedUrl), 'provider');

        $provider = $this->createMock(UserProviderInterface::class);
        $provider->expects($this->any())
            ->method('loadUserByUsername')
            ->with($user->getEmail())
            ->will($this->returnValue($user));

        // bump the last login date so the url is no longer valid
        $user->setLastLogin(new \DateTime());

        $this->assertNull($this->auth->authenticateToken($token, $provider, 'provider'));
    }
}
