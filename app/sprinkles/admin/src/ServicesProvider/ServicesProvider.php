<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/UserFrosting
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/licenses/UserFrosting.md (MIT License)
 */
namespace UserFrosting\Sprinkle\Admin\ServicesProvider;

use UserFrosting\Sprinkle\Account\Authenticate\Authenticator;
use UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager;
use UserFrosting\Sprinkle\Admin\Http\Redirector;
use UserFrosting\Sprinkle\Core\Facades\Debug;

/**
 * Registers services for the admin sprinkle.
 *
 * @author Alex Weissman (https://alexanderweissman.com)
 */
class ServicesProvider
{
    /**
     * Register UserFrosting's admin services.
     *
     * @param Container $container A DI container implementing ArrayAccess and container-interop.
     */
    public function register($container)
    {
        /**
         * Extend the 'classMapper' service to register sprunje classes.
         *
         * Mappings added: 'activity_sprunje', 'group_sprunje', 'permission_sprunje', 'role_sprunje', 'user_sprunje'
         */
        $container->extend('classMapper', function ($classMapper, $c) {
            $classMapper->setClassMapping('activity_sprunje', 'UserFrosting\Sprinkle\Admin\Sprunje\ActivitySprunje');
            $classMapper->setClassMapping('group_sprunje', 'UserFrosting\Sprinkle\Admin\Sprunje\GroupSprunje');
            $classMapper->setClassMapping('permission_sprunje', 'UserFrosting\Sprinkle\Admin\Sprunje\PermissionSprunje');
            $classMapper->setClassMapping('permission_user_sprunje', 'UserFrosting\Sprinkle\Admin\Sprunje\PermissionUserSprunje');
            $classMapper->setClassMapping('role_sprunje', 'UserFrosting\Sprinkle\Admin\Sprunje\RoleSprunje');
            $classMapper->setClassMapping('user_sprunje', 'UserFrosting\Sprinkle\Admin\Sprunje\UserSprunje');
            $classMapper->setClassMapping('user_permission_sprunje', 'UserFrosting\Sprinkle\Admin\Sprunje\UserPermissionSprunje');
            return $classMapper;
        });

        /**
         * Handles redirects after account-related actions (login, etc).
         */
        $container['redirector'] = function ($c) {
            $redirector = new Redirector($c);
            return $redirector;
        };
    }
}
