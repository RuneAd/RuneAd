<?php
class AclList {

    private $roles;
    public $resources;

    /**
     * @param Role registers a new role
     */
    public function addRole($role) {
        $this->roles[] = $role;
    }

    /**
     * @return array of roles
     */
    public function getRoles() {
        return $this->roles;
    }

    /**
     * registers a new resource
     * @param string $name
     * @param Resource $resource
     */
    public function addResource($name, $resource) {
        $this->resources[] = $resource;
    }

    public function getRole($name) {
        foreach ($this->roles as $role) {
            if (strtolower($role->getName()) == strtolower($name)) {
                return $name;
            }
        }
        return null;
    }

    /**
     * Gets all resources if $name is null, or grabs resources by their name
     * @param string $name
     * @return Resource array of resources matching the name
     */
    public function getResources($name = null) {
        if ($name == null) {
            return $this->resources;
        }

        $resources = [];

        foreach ($this->resources as $resource) {
            if (strtolower($resource->getName()) == strtolower($name)) {
                $resources[] = $resource;
            }
        }
        return $resources;
    }

}
