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
            'index'  => ['index', 'logout', 'details'],
            'login'  => ['index', 'discord', 'auth', 'dauth'],
            'topic'  => ['view'],
            'errors' => ['show404', 'show500', 'show401']
        ];

        $private = [
            'topic'  => ['edit'],
        ];

        $moderator = [
            'topic'  => ['deletetopic'],
        ];

        foreach ($public as $controller => $actions) {
            $resource = new Resource($controller, $actions);

            $resource->allow($acl->getRole('Owner'));
            $resource->allow($acl->getRole('Administrator'));
            $resource->allow($acl->getRole('Member'));
            $resource->allow($acl->getRole('Server Owner'));
            $resource->allow($acl->getRole('Guest'));

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

        // iterate user's roles and build of roles
        foreach ($roles as $user_role) {
            if ($role = $accessList->getRole($user_role)) {
                $roleList[] = $role;
            }
        }

        foreach ($resources as $resource) {
            if ($resource->isAllowed($roleList, $action)) {
                echo "can access $controller $action";
                return true;
            }
        }
        return false;
    }


}
