# Roles And Permissions For Laravel 5

Powerful package for handling roles and permissions in Laravel 5 (5.1 and also 5.0).

- [Installation](#installation)
    - [Composer](#composer)
    - [Service Provider](#service-provider)
    - [Config File And Migrations](#config-file-and-migrations)
    - [HasRoleAndPermission Trait And Contract](#hasroleandpermission-trait-and-contract)
- [Usage](#usage)
    - [Creating Roles](#creating-roles)
    - [Attaching And Detaching Roles](#attaching-and-detaching-roles)
    - [Checking For Roles](#checking-for-roles)
    - [Levels](#levels)
    - [Creating Permissions](#creating-permissions)
    - [Attaching And Detaching Permissions](#attaching-and-detaching-permissions)
    - [Checking For Permissions](#checking-for-permissions)
    - [Permissions Inheriting](#permissions-inheriting)
    - [Deny A Permission](#deny-a-permission)
    - [Entity Check](#entity-check)
    - [Blade Extensions](#blade-extensions)
    - [Middleware](#middleware)
- [Config File](#config-file)
- [More Information](#more-information)
- [License](#license)

## Installation

This package is very easy to set up. There are only couple of steps.

### Composer

Pull this package in through Composer (file `composer.json`).

```js
{
    "require": {
        "php": ">=5.5.9",
        "laravel/framework": "5.1.*",
        "bican/roles": "2.1.*"
    }
}
```

> If you are still using Laravel 5.0, you must pull in version `1.7.*`.

Run this command inside your terminal.

    composer update

### Service Provider

Add the package to your application service providers in `config/app.php` file.

```php
'providers' => [
    
    /*
     * Laravel Framework Service Providers...
     */
    Illuminate\Foundation\Providers\ArtisanServiceProvider::class,
    Illuminate\Auth\AuthServiceProvider::class,
    ...
    
    /**
     * Third Party Service Providers...
     */
    Bican\Roles\RolesServiceProvider::class,

],
```

### Config File And Migrations

Publish the package config file and migrations to your application. Run these commands inside your terminal.

    php artisan vendor:publish --provider="Bican\Roles\RolesServiceProvider" --tag=config
    php artisan vendor:publish --provider="Bican\Roles\RolesServiceProvider" --tag=migrations

And also run migrations.

    php artisan migrate

> There must be created migration file for users table, which is in Laravel out of the box.

### HasRoleAndPermission Trait And Contract

Include `HasRoleAndPermission` trait and also implement `HasRoleAndPermission` contract inside your `User` model.

```php
use Bican\Roles\Traits\HasRoleAndPermission;
use Bican\Roles\Contracts\HasRoleAndPermission as HasRoleAndPermissionContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract, HasRoleAndPermissionContract
{
    use Authenticatable, CanResetPassword, HasRoleAndPermission;
```

And that's it!

## Usage

### Creating Roles

```php
use Bican\Roles\Models\Role;

$adminRole = Role::create([
    'name' => 'Admin',
    'slug' => 'admin',
    'description' => '', // optional
    'level' => 1, // optional, set to 1 by default
]);

$moderatorRole = Role::create([
    'name' => 'Forum Moderator',
    'slug' => 'forum.moderator',
]);
```

> Because of `Slugable` trait, if you make a mistake and for example leave a space in slug parameter, it'll be replaced with a dot automatically, because of `str_slug` function.

### Attaching And Detaching Roles

It's really simple. You fetch a user from database and call `attachRole` method. There is `BelongsToMany` relationship between `User` and `Role` model.

```php
use App\User;

$user = User::find($id);

$user->attachRole($adminRole); // you can pass whole object, or just an id
```

```php
$user->detachRole($adminRole); // in case you want to detach role
$user->detachAllRoles(); // in case you want to detach all roles
```

### Checking For Roles

You can now check if the user has required role.

```php
if ($user->is('admin')) { // you can pass an id or slug
    //
}
```

You can also do this:

```php
if ($user->isAdmin()) {
    //
}
```

And of course, there is a way to check for multiple roles:

```php
if ($user->is('admin|moderator')) { // or $user->is('admin, moderator') and also $user->is(['admin', 'moderator'])
    // if user has at least one role
}

if ($user->is('admin|moderator', true)) { // or $user->is('admin, moderator', true) and also $user->is(['admin', 'moderator'], true)
    // if user has all roles
}
```

### Levels

When you are creating roles, there is optional parameter `level`. It is set to `1` by default, but you can overwrite it and then you can do something like this:
 
```php
if ($user->level() > 4) {
    //
}
```

> If user has multiple roles, method `level` returns the highest one.

`Level` has also big effect on inheriting permissions. About it later.

### Creating Permissions

It's very simple thanks to `Permission` model.

```php
use Bican\Roles\Models\Permission;

$createUsersPermission = Permission::create([
    'name' => 'Create users',
    'slug' => 'create.users',
    'description' => '', // optional
]);

$deleteUsersPermission = Permission::create([
    'name' => 'Delete users',
    'slug' => 'delete.users',
]);
```

### Attaching And Detaching Permissions

You can attach permissions to a role or directly to a specific user (and of course detach them as well).

```php
use App\User;
use Bican\Roles\Models\Role;

$role = Role::find($roleId);
$role->attachPermission($createUsersPermission); // permission attached to a role

$user = User::find($userId);
$user->attachPermission($deleteUsersPermission); // permission attached to a user
```

```php
$role->detachPermission($createUsersPermission); // in case you want to detach permission
$role->detachAllPermissions(); // in case you want to detach all permissions

$user->detachPermission($deleteUsersPermission);
$user->detachAllPermissions();
```

### Checking For Permissions

```php
if ($user->can('create.users') { // you can pass an id or slug
    //
}

if ($user->canDeleteusers()) {
    //
}
```

You can check for multiple permissions the same way as roles.

### Permissions Inheriting

Role with higher level is inheriting permission from roles with lower level.

There is an example of this `magic`:

You have three roles: `user`, `moderator` and `admin`. User has a permission to read articles, moderator can manage comments and admin can create articles. User has a level 1, moderator level 2 and admin level 3. It means, moderator and administrator has also permission to read articles, but administrator can manage comments as well.

> If you don't want permissions inheriting feature in you application, simply ignore `level` parameter when you're creating roles.

### Deny A Permission

Easily deny a user a specific permission regardless of what role the user is apart of.

```php
use App\User;
use Bican\Roles\Models\Role;

$user = User::find($userId);
$user->attachPermission($deleteUsersPermission, false); // permission attached to a user, specially to deny that permission 
```

### Entity Check

Let's say you have an article and you want to edit it. This article belongs to a user (there is a column `user_id` in articles table).

```php
use App\Article;
use Bican\Roles\Models\Permission;

$editArticlesPermission = Permission::create([
    'name' => 'Edit articles',
    'slug' => 'edit.articles',
    'model' => 'App\Article',
]);

$user->attachPermission($editArticlesPermission);

$article = Article::find(1);

if ($user->allowed('edit.articles', $article)) { // $user->allowedEditArticles($article)
    //
}
```

This condition checks if the current user is the owner of article. If not, it will be looking inside user permissions for a row we created before.

```php
if ($user->allowed('edit.articles', $article, false)) { // now owner check is disabled
    //
}
```

### Blade Extensions

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

### Middleware

This package comes with `VerifyRole` and `VerifyPermission` middleware. You must add them inside your `app/Http/Kernel.php` file.

```php
/**
 * The application's route middleware.
 *
 * @var array
 */
protected $routeMiddleware = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    'role' => \Bican\Roles\Middleware\VerifyRole::class,
    'permission' => \Bican\Roles\Middleware\VerifyPermission::class,
];
```

Now you can easily protect your routes.

```php
$router->get('/example', [
    'as' => 'example',
    'middleware' => 'role:admin',
    'uses' => 'ExampleController@index',
]);

$router->post('/example', [
    'as' => 'example',
    'middleware' => 'permission:edit.articles',
    'uses' => 'ExampleController@index',
]);
```

It throws `\Bican\Roles\Exception\AccessDeniedException` if it goes wrong.

You can catch this exception inside `app/Exceptions/Handler.php` file and do whatever you want.

```php
/**
 * Render an exception into an HTTP response.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  \Exception  $e
 * @return \Illuminate\Http\Response
 */
public function render($request, Exception $e)
{
    if ($e instanceof \Bican\Roles\Exceptions\AccessDeniedException) {
        // you can for example flash message, redirect...
        return redirect()->back();
    }

    return parent::render($request, $e);
}
```

## Config File

You can change connection for models, slug separator, models path and there is also a handy pretend feature. Have a look at config file for more information.

## More Information

For more information, please have a look at [HasRoleAndPermission](https://github.com/romanbican/roles/blob/master/src/Bican/Roles/Contracts/HasRoleAndPermission.php) contact.

## License

This package is free software distributed under the terms of the MIT license.
