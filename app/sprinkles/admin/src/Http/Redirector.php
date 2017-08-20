<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/UserFrosting
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/licenses/UserFrosting.md (MIT License)
 */
namespace UserFrosting\Sprinkle\Admin\Http;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use UserFrosting\Sprinkle\Account\Http\Redirector as AccountRedirector;

/**
 * Handles common redirections in account-related controller methods.
 *
 * Overrides/extends base functionality in the account Sprinkle.
 * @author Alex Weissman (https://alexanderweissman.com)
 */
class Redirector extends AccountRedirector
{
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

        if ($authorizer->checkAccess($currentUser, 'uri_dashboard')) {
            return $response->withHeader('UF-Redirect', $this->ci->router->pathFor('dashboard'));
        } elseif ($authorizer->checkAccess($currentUser, 'uri_account_settings')) {
            return $response->withHeader('UF-Redirect', $this->ci->router->pathFor('settings'));
        } else {
            return $response->withHeader('UF-Redirect', $this->ci->router->pathFor('index'));
        }
    }
}
