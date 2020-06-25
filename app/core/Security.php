<?php
class Security {

    /**
     * @return AclList access list
     */
    public static function getAclList() {
        $acl = new AclList();

        // register available roles.
        $acl->addRole(new Role('Owner'));
        $acl->addRole(new Role('Administrator'));
        $acl->addRole(new Role('Server Owner'));
        $acl->addRole(new Role('Member'));
        $acl->addRole(new Role('Guest'));

        // define controlsl and actions for a group
        $public = [
            'index'   => ['index', 'logout', 'details'],
            'vote'    => ['index', 'addvote'],
            'premium' => ['index'],
            'sponsor' => ['index'],
            'login'   => ['index', 'discord', 'auth', 'dauth'],
            'errors'  => ['show404', 'show500', 'show401', 'missing'],
            'pages'   => ['docs', 'updates', 'stats', 'terms', 'privacy'],
            'tools'   => ['itemdb', 'search']
        ];

        $private = [
            'servers' => ['add', 'edit', 'delete'],
            'premium' => ['button', 'verify', 'process'],
            'sponsor' => ['button', 'verify', 'process'],
            'profile' => ['index'],
        ];

        $admin = [
          'admin'    => ['index'],
          'payments' => ['index']
        ];

        foreach ($public as $controller => $actions) {
            $resource = new Resource($controller, $actions);

            $resource->allow([
                $acl->getRole('Owner'),
                $acl->getRole('Administrator'),
                $acl->getRole('Member'),
                $acl->getRole('Server Owner'),
                $acl->getRole('Guest'),
            ]);

            $acl->addResource($controller, $resource);
        }

        foreach ($private as $controller => $actions) {
            $resource = new Resource($controller, $actions);

            $resource->allow([
                $acl->getRole('Owner'),
                $acl->getRole('Administrator'),
                $acl->getRole('Member'),
                $acl->getRole('Server Owner')
            ]);

            $acl->addResource($controller, $resource);
        }

        foreach ($admin as $controller => $actions) {
            $resource = new Resource($controller, $actions);

            $resource->allow([
                $acl->getRole('Owner'),
                $acl->getRole('Administrator')
            ]);

            $acl->addResource($controller, $resource);
        }
        return $acl;
    }

    /**
     * @param string $controller
     * @param string $action
     * @param array $roles
     * @return true if user has access to the page.
     */
    public static function canAccess($controller, $action, $roles) {
        $accessList = self::getAclList();
        $roleList   = [];
        $resources  = $accessList->getResources($controller);

        // if resource isn't in list, then deny access
        // ...just to be on the safe side :D
        if (!$resources || empty($resources)) {
            return false;
        }

        // iterate user's roles and build roles list
        foreach ($roles as $user_role) {
            if ($role = $accessList->getRole($user_role)) {
                $roleList[] = $role;
            }
        }

        foreach ($resources as $resource) {
            if ($resource->isAllowed($roleList, $action)) {
                return true;
            }
        }
        return false;
    }


}
