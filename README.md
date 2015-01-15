# Roles for Laravel 5

Simple package for handling user roles in Laravel 5.

## Install

Pull this package in through Composer.

```js
{
  "require": {
    "bican/roles": "~0.9"
  }
}
```

Then you need to create and run migrations. You can find them in src/migrations directory. Migration for users table is in Laravel 5 out of the box.

## Usage

First, include HasRole trait inside your User model.

```php
...

use Bican\Roles\HasRole;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword, HasRole;

...
```

... and actually you are set to go!

Now you can create roles and attach them to a user.

```php
$role = \Bican\Roles\Role::create(['name' => 'admin']);

$user = App\User::find(1)->attach($role); // you can pass whole object or just id
```

HasRole trait contains couple of methods:

```php
$roles = $user->roles()->get(); // all roles

$check = $user->hasRole('admin'); // or you can pass id

if ($check)
{
  return 'admin';
}

$user->attach($role); // you can use whole object or just id
$user->detach($role); // you can use whole object or just id
```
