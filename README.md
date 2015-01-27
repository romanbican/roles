# Roles and permissions for Laravel 5

Simple package for handling roles and permissions in Laravel 5.

## Install

Pull this package in through Composer.

```js
{
    "require": {
        "bican/roles": "~1.1"
    }
}
```

    $ composer update

Then create and run migrations. You can copy them from `src/migrations` directory. Migration for users table is in Laravel 5 out of the box.

    $ php artisan migrate

## Simple usage (roles only)

First of all, include `HasRole` trait and `HasRoleContract` interface inside your `User` model.

```php
use Bican\Roles\Contracts\HasRoleContract;
use Bican\Roles\HasRole;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract, HasRoleContract {

	use Authenticatable, CanResetPassword, HasRole;
```

Now you can create role and assign it to a user.

```php
use Bican\Roles\Role;
use App\User;

$role = Role::create([
    'label' => 'admin',
    'name' => 'Administrator'
]);

$user = User::find($id)->assignRole($role); // you can pass whole object, or just id
```

Then you can simply check if the current user has required role.

```php
if ($user->is('admin')) // or you can pass id
{
    return 'admin';
}
```

You can also do this:

```php
if ($user->isAdmin())
{
    return 'admin';
}
```
But remember, `label` must start with lowercase letter and if role has multiple words, it must look like this: "word_and_another".

## Advanced usage (with permissions)

If you are building more complex application, you can also use permissions and attach them to a role (and of course detach as well).
You need to set `unique` parameter to "1", if you don't want to use "levels" (about it later).

```php
use Bican\Roles\Permission;
use Bican\Roles\Role;

$permission = Permission::create([
    'label' => 'edit_articles',
    'name' => 'Edit articles',
    'unique' => 1
]);

Role::find($id)->attachPermission($permission);

if ($user->can('edit_articles') // or you can pass id
{
    return 'he has permission!';
}

if ($user->canEditArticles())
{
    //
}

Role::find($id)->detachPermission($permission);
```

You can also check for multiple permission in one method.

```php
if ($user->can('edit_articles|manage_comments')
{
    // if he has at least one permission, this code will be executed.
}

if ($user->can('edit_articles|manage_comments', 'all')
{
    // if he has all provided permissions, this code will be executed.
}
```

## Advanced usage (with levels = inheriting permissions)

Now, this package is aware of `role levels`. There is an example:

You have three roles: `user`, `moderator` and `admin`. User has a permission to read articles, moderator can manage comments and admin can create articles.

User has a level 1 (it can be set inside `roles table`), moderator level 2 and admin level 3. If you don't set column `unique` in permissions table to value "1", moderator and administrator has also permission to read articles, but administrator can also manage comments as well.

So role with higher level inherits permissions from lower level if `unique` is set to null.


