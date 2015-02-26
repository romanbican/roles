# Roles and permissions for Laravel 5

Powerful package for handling roles and permissions in Laravel 5.

**Change in 1.4.0: Words in slug are now separated with dots instead of underscores!**

## Install

Pull this package in through Composer.

```js
{
    "require": {
        "bican/roles": "1.4.*"
    }
}
```

    $ composer update

Add the package to your application service providers in `config/app.php`

```php
'providers' => [
    
    'Illuminate\Foundation\Providers\ArtisanServiceProvider',
    'Illuminate\Auth\AuthServiceProvider',
    ...
    
    'Bican\Roles\RolesServiceProvider',

],
```

Publish the package migrations to your application.

    $ php artisan vendor:publish

Run migrations.

    $ php artisan migrate

## Usage

First of all, include `HasRole`, `HasPermission` traits and also implement their interfaces `HasRoleContract` and `HasPermissionContract` inside your `User` model.

```php
use Bican\Roles\Contracts\HasRoleContract;
use Bican\Roles\Contracts\HasPermissionContract;
use Bican\Roles\Traits\HasRole;
use Bican\Roles\Traits\HasPermission;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract, HasRoleContract, HasPermissionContract {

	use Authenticatable, CanResetPassword, HasRole, HasPermission;
```

You're set to go. You can create your first role and attach it to a user.

```php
use Bican\Roles\Models\Role;
use App\User;

$role = Role::create([
    'name' => 'Admin',
    'slug' => 'admin',
    'description' => '' // optional
]);

$user = User::find($id)->attachRole($role); // you can pass whole object, or just id
```

You can simply check if the current user has required role.

```php
if ($user->is('admin')) // here checking by slug. You can pass an id or slug
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

And of course, there is a way to check for multiple roles:

```php
if ($user->is('admin|moderator')) // or $user->is('admin, moderator') and also $user->is(['admin', 'moderator'])
{
    // if user has at least one role
}

if ($user->is('admin|moderator', 'all')) // or $user->is('admin, moderator', 'all') and also $user->is(['admin', 'moderator'], 'all')
{
    // if user has all roles
}
```

When you are creating roles, there is also optional parameter `level`. It is set to `1` by default, but you can overwrite it and then you can do something like this:
 
```php
if ($user->level() > 4)
{
    // code
}
```

If user has multiple roles, method `level` returns the highest one.

`Level` has also big effect on inheriting permissions. About it later.

Let's talk about permissions. You can attach permission to a role or directly to a specific user (and of course detach them as well).

```php
use Bican\Roles\Models\Permission;
use Bican\Roles\Models\Role;

$permission = Permission::create([
    'name' => 'Edit articles',
    'slug' => 'edit.articles',
    'description' => '' // optional
]);

Role::find($id)->attachPermission($permission);

$user->attachPermission($anotherPermission);

if ($user->can('edit.articles') // you can pass an id or slug
{
    return 'he has permission!';
}

if ($user->canAnotherPermission())
{
    //
}

```

You can check for multiple permissions the same way as roles.

## Permissions Inheriting

Permissions attach to a specific user are unique by default. Role permissions not, but you can do it by passing optional parameter `unique` when creating and set it to `1`.

Anyways, role with higher level is inheriting permission from roles with lower level.

There is an example of this `magic`: You have three roles: `user`, `moderator` and `admin`. User has a permission to read articles, moderator can manage comments and admin can create articles. User has a level 1, moderator level 2 and admin level 3. If you don't set column `unique` in permissions table to value `1`, moderator and administrator has also permission to read articles, but administrator can manage comments as well.

## Entity Check

Let's say you have an article and you want to edit it. This article belongs to a user (`user_id` in database).

```php
$user->attachPermission([
    'slug' => 'edit',
    'name' => 'Edit articles',
    'model' => 'App\Article'
]);

$article = \App\Article::find(1);

if ($user->allowed('edit', $article)) // $user->allowedEdit($article)
{
    $article->save();
}
```

This condition checks if the current user is the owner of provided article. If not, it will be looking inside user permissions for a row we created before.

```php
if ($user->allowed('edit', $article, false)) // now owner check is disabled
{
    $article->save();
}
```

## Blade Extensions

There are two Blade extensions. Basically, it is replacement for classic if statements.

```php
@role('admin') // @if(Auth::check() && Auth::user()->is('admin'))
    // user is admin
@endrole

@permission('edit.articles')
    // user can edit articles
@endpermission

@allowed('edit', $article)
    // show edit button
@endallowed

@role('admin|moderator', 'all')
    // user is admin and also moderator
@else
    // something else
@endrole
```