# Roles and permissions for Laravel 5

Powerful package for handling roles and permissions in Laravel 5 (5.1 and also 5.0).

## Install

**Changes in version 2.0.0! Please read the documentation if you want to upgrade.**

Pull this package in through Composer. For Laravel 5.0, have a look at tag `1.7.*`.

```js
{
    "require": {
        "bican/roles": "2.0.*"
    }
}
```

    $ composer update

Add the package to your application service providers in `config/app.php`

```php
'providers' => [
    
    /*
     * Laravel Framework Service Providers...
     */
    Illuminate\Foundation\Providers\ArtisanServiceProvider::class,
    Illuminate\Auth\AuthServiceProvider::class,
    ...
    
    Bican\Roles\RolesServiceProvider::class,

],
```

Publish the package migrations and config file to your application.

    $ php artisan vendor:publish --provider="Bican\Roles\RolesServiceProvider" --tag=config
    $ php artisan vendor:publish --provider="Bican\Roles\RolesServiceProvider" --tag=migrations

Run migrations.

    $ php artisan migrate

### Configuration file

You can change connection for models, slug separator, models path and there is also a handy pretend feature. Have a look at config file for more information.

## Usage

First of all, include `HasRoleAndPermission` trait and also implement `HasRoleAndPermission` contract inside your `User` model.

```php
use Bican\Roles\Traits\HasRoleAndPermission;
use Bican\Roles\Contracts\HasRoleAndPermission as HasRoleAndPermissionContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract, HasRoleAndPermissionContract {

	use Authenticatable, CanResetPassword, HasRoleAndPermission;
```

You're set to go. You can create your first role and attach it to a user.

```php
use App\User;
use Bican\Roles\Models\Role;

$role = Role::create([
    'name' => 'Admin',
    'slug' => 'admin',
    'description' => '', // optional
]);

$user = User::find($id);

$user->attachRole($role); // you can pass whole object, or just an id
```

You can simply check if the current user has required role.

```php
if ($user->is('admin')) // you can pass an id or slug
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

if ($user->is('admin|moderator', true)) // or $user->is('admin, moderator', true) and also $user->is(['admin', 'moderator'], true)
{
    // if user has all roles
}
```

When you are creating roles, there is also optional parameter `level`. It is set to `1` by default, but you can overwrite it and then you can do something like this:
 
```php
if ($user->level() > 4)
{
    //
}
```

If user has multiple roles, method `level` returns the highest one.

`Level` has also big effect on inheriting permissions. About it later.

Let's talk about permissions in general. You can attach permission to a role or directly to a specific user (and of course detach them as well).

```php
use Bican\Roles\Models\Role;
use Bican\Roles\Models\Permission;

$permission = Permission::create([
    'name' => 'Edit articles',
    'slug' => 'edit.articles',
    'description' => '', // optional
]);

$role = Role::find($roleId);
$user = User::find($userId);

$role->attachPermission($permission); // permission attached to a role

$user->attachPermission($permission); // permission attached to a user

if ($user->can('edit.articles') // you can pass an id or slug
{
    //
}

if ($user->canEditArticles())
{
    //
}
```

You can check for multiple permissions the same way as roles.

## Permissions Inheriting

Role with higher level is inheriting permission from roles with lower level.

There is an example of this `magic`: You have three roles: `user`, `moderator` and `admin`. User has a permission to read articles, moderator can manage comments and admin can create articles. User has a level 1, moderator level 2 and admin level 3. It means, moderator and administrator has also permission to read articles, but administrator can manage comments as well.

## Entity Check

Let's say you have an article and you want to edit it. This article belongs to a user (`user_id` in database).

```php
use App\Article;

$user->attachPermission([
    'slug' => 'edit',
    'name' => 'Edit articles',
    'model' => 'App\Article',
]);

$article = Article::find(1);

if ($user->allowed('edit', $article)) // $user->allowedEdit($article)
{
    //
}
```

This condition checks if the current user is the owner of provided article. If not, it will be looking inside user permissions for a row we created before.

```php
if ($user->allowed('edit', $article, false)) // now owner check is disabled
{
    //
}
```

## Blade Extensions

There are three Blade extensions. Basically, it is replacement for classic if statements.

```php
@role('admin') // @if(Auth::check() && Auth::user()->is('admin'))
    // user is admin
@endrole

@permission('edit.articles') // @if(Auth::check() && Auth::user()->can('edit.articles'))
    // user can edit articles
@endpermission

@allowed('edit', $article) // @if(Auth::check() && Auth::user()->allowed('edit', $article))
    // show edit button
@endallowed

@role('admin|moderator', 'all') // @if(Auth::check() && Auth::user()->is('admin|moderator', 'all'))
    // user is admin and also moderator
@else
    // something else
@endrole
```

For more information, please have a look at contract [HasRoleAndPermission](https://github.com/romanbican/roles/blob/master/src/Bican/Roles/Contracts/HasRoleAndPermission.php).
