# Roles and permissions for Laravel 5

Simple package for handling roles and permissions in Laravel 5.

## Install

Pull this package in through Composer.

```js
{
  "require": {
    "bican/roles": "~1.0"
  }
}
```

Then you need to create and run migrations. You can find them in src/migrations directory. Migration for users table is in Laravel 5 out of the box.

## Usage

First of all, include HasRole trait inside your User model.

```php
...

use Bican\Roles\HasRole;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword, HasRole;

...
```

... and actually you are set to go!

Now you can create role and attach it to a user.

```php
$role = \Bican\Roles\Role::create(['label' => 'admin', 'name' => 'Administrator']);

$user = \App\User::find($id)->changeRole($role); // you can pass whole object, or just id
```

Then you can simply check if the current user has required role:

```php
if ($user->hasRole('admin') // or you can pass id
{
  return 'admin';
}
```

If you are building more complex application, you can also use permissions and attach them to a role (and of course detach as well).
You need to set unique parameter to "1" if you don't want to use "levels" (about it later).

```php
$permission = \Bican\Roles\Permission::create(['label' => 'edit_articles', 'name' => 'Edit articles', 'unique' => 1]);

\Bican\Roles\Role::find($id)->attachPermission($permission);

if ($user->hasPermission('edit_articles') // or you can pass id
{
  return 'he has permission!';
}

\Bican\Roles\Role::find($id)->detachPermission($permission);
```

Now, this package is aware of role levels. There is an example:

You have three roles: user, moderator and admin. User has permission to read articles, moderator can manage comments and admin can create articles.

User has level 1, moderator 2 and admin 3. If you don't set column "unique" in permissions table to value "1", moderator has also permission to read articles and administrator too, but administrator can also manage comments as well.


