## Examples

### Create new Role

```php

use WCWP\Role;

$role = new Role();
$role->setName('manager')
     ->setDisplayName('Manager')
     ->useCapabilitiesFrom('administrator')
     ->disallow(
        /** Core */
        'update_core',
        /** Plugins */
        "delete_plugins",
        "edit_plugins",
        "install_plugins",
        "update_plugins",
        'upload_plugins',
        'activate_plugins',
        /** Themes */
        'update_themes',
        'edit_themes',
        /** Others */
        'import',
        'export',
        'unfiltered_html'
     )
     ->allow('export')
     ->save();

```

### Get role

```php

use WCWP\Role;

$role = Role::getRole('administrator');

```

### Remove role

```php

use WCWP\Role;

$role = Role::removeRole('administrator');

```