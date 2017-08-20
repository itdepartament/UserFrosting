<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/UserFrosting
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/licenses/UserFrosting.md (MIT License)
 */
namespace UserFrosting\Sprinkle\Account\Http;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Handles common redirections in account-related controller methods.
 *
 * @author Alex Weissman (https://alexanderweissman.com)
 */
class Redirector
{
    /**
     * @var \Interop\Container\ContainerInterface
     */
    protected $ci;

    /**
     * Constructor.
     *
     * @param \Interop\Container\ContainerInterface $ci
     */
    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    /**
     * This method is invoked when a user attempts to perform certain public actions when they are already logged in.
     *
     * @todo Forward to user's landing page or last visited page
     * @param \Psr\Http\Message\ServerRequestInterface $request  
     * @param \Psr\Http\Message\ResponseInterface      $response 
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function onAlreadyLoggedIn(Request $request, Response $response, array $args)
    {
        $redirect = $this->ci->router->pathFor('dashboard');

        return $response->withRedirect($redirect, 302);
    }

    /**
     * This method is invoked when a user completes the login process.
     *
     * Returns a callback that handles setting the `UF-Redirect` header after a successful login.
     * @param \Psr\Http\Message\ServerRequestInterface $request  
     * @param \Psr\Http\Message\ResponseInterface      $response 
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function onLogin(Request $request, Response $response, array $args)
    {
        // Backwards compatibility for the deprecated determineRedirectOnLogin service
        if ($this->ci->has('determineRedirectOnLogin')) {
            $determineRedirectOnLogin = $this->ci->determineRedirectOnLogin;
    
            return $determineRedirectOnLogin($response)->withStatus(200);
        }

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        $currentUser = $this->ci->authenticator->user();

        if ($authorizer->checkAccess($currentUser, 'uri_account_settings')) {
            return $response->withHeader('UF-Redirect', $this->ci->router->pathFor('settings'));
        } else {
            return $response->withHeader('UF-Redirect', $this->ci->router->pathFor('index'));
        }
    }
}
