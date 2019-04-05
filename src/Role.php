<?php

namespace WPWC;

class Role
{
    /**
     * @property string $name Role name.
     */
    private $name;

    /**
     * @property string $displayName Role's display name.
     */
    private $displayName;

    /**
     * @property array $capabilities Array of capabilities.
     */
    private $capabilities = [];

    /**
     * Get role by name.
     *
     * @return WP_Role|null WP_Role on success, null on failure.
     */
    public static function getRole(string $name)
    {
        return get_role($name);
    }

    /**
     * Set role name.
     *
     * @param string $name Role name.
     * @return Role
     */
    public function setName(string $name) : Role
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get role name.
     *
     * @return string Role name.
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Set role display name.
     *
     * @param string $name Role display name.
     * @return Role
     */
    public function setDisplayName(string $name) : Role
    {
        $this->displayName = $name;

        return $this;
    }

    /**
     * Get role display name.
     *
     * @return string Role display name.
     */
    public function getDisplayName() : string
    {
        return $this->displayName;
    }

    /**
     * Save role information to database using add_role() function
     *
     * @see https://codex.wordpress.org/Function_Reference/add_role
     * @return WP_Role|null WP_Role object on success, null if that role already exists.
     */
    public function save()
    {
        /**
         * Remove role if it already exists to avoid add_role() short-circuit so we can update role data.
         */
        if (self::getRole($this->name)) {
            self::removeRole($this->name);
        }

        return add_role($this->name, $this->displayName, $this->capabilities);
    }

    /**
     * Set role capabilities based on other role's capabilities.
     *
     * @param string $roleName Name of role its capabilities will be used.
     * @return Role
     */
    public function useCapabilitiesFrom(string $roleName) : Role
    {
        $role = get_role($roleName);
        
        if (is_null($role)) {
            trigger_error("Role '$roleName' not found", E_USER_ERROR);
        }
        
        $caps = $role->capabilities;

        $this->capabilities = array_merge($this->capabilities, $caps);

        return $this;
    }

    /**
     * Add capabilities to the role.
     *
     * @param string $capabilities,... Unlimited number of capabilities name to be added to this role.
     * @return Role
     */
    public function allow(string ...$capabilities) : Role
    {
        $this->capabilites = array_merge($this->capabilities, $capabilities);

        return $this;
    }

    /**
     * Remove capabilities from the role.
     *
     * @param string $capabilities,... Unlimited number of capabilities name to be removed from the role.
     * @return Role
     */
    public function disallow(string ...$capabilities) : Role
    {
        $this->capabilites = array_diff($this->capabilities, $capabilities);

        return $this;
    }
}
