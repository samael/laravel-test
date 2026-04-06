# Spatie Laravel Permission Setup Guide

## Overview

This project uses **Spatie Laravel Permission** for role-based access control (RBAC).

## Installed Roles

1. **manager** - Can manage tickets and view them
2. **admin** - Full access: manage tickets, view reports, manage users

## Permissions

- `view tickets` - View ticket list
- `manage tickets` - Create, edit, delete tickets
- `view reports` - View analytics and reports
- `manage users` - Manage user accounts and roles

## Role-Permission Mapping

| Role      | Permissions                             |
|-----------|----------------------------------------|
| manager   | view tickets, manage tickets            |
| admin     | manage tickets, view reports, manage users, view tickets |

## Usage in Code

### Check User Role

```php
$user = Auth::user();

// Check single role
if ($user->hasRole('admin')) {
    // User is admin
}

// Check if user has any of the given roles
if ($user->hasAnyRole(['admin', 'manager'])) {
    // User is admin or manager
}

// Check if user has all roles
if ($user->hasAllRoles(['admin', 'manager'])) {
    // User has both roles
}
```

### Check User Permission

```php
$user = Auth::user();

// Check single permission
if ($user->hasPermissionTo('manage tickets')) {
    // User can manage tickets
}

// Check if user has any permission
if ($user->hasAnyPermission(['manage tickets', 'manage users'])) {
    // User can do one of these
}

// Check if user has all permissions
if ($user->hasAllPermissions(['manage tickets', 'manage users'])) {
    // User can do both
}
```

### With Blade Directives

```blade
@role('admin')
    <!-- Visible only to admins -->
@endrole

@hasrole('manager')
    <!-- Visible only to managers -->
@endhasrole

@can('manage tickets')
    <!-- User has permission to manage tickets -->
@endcan

@cannot('manage users')
    <!-- User does NOT have permission to manage users -->
@endcannot
```

### With Route Middleware

```php
// In routes/web.php

// Protect route with role
Route::get('/admin-dashboard', [AdminController::class, 'dashboard'])->middleware('role:admin');

// Protect route with permission
Route::post('/tickets', [TicketController::class, 'store'])->middleware('permission:manage tickets');

// Multiple roles (user needs at least one)
Route::get('/tickets', [TicketController::class, 'index'])->middleware('role:admin,manager');

// Multiple permissions (user needs all)
Route::delete('/tickets/{id}', [TicketController::class, 'destroy'])
    ->middleware('permission:manage tickets,view tickets');
```

### Assigning Roles and Permissions

```php
use App\Models\User;
use Spatie\Permission\Models\Role;

$user = User::find(1);

// Assign role
$user->assignRole('manager');

// Assign multiple roles
$user->assignRole(['manager', 'admin']);

// Remove role
$user->removeRole('manager');

// Sync roles (replaces existing)
$user->syncRoles(['admin', 'manager']);

// Give permission directly (not via role)
$user->givePermissionTo('manage tickets');
```

### Working with Roles

```php
use Spatie\Permission\Models\Role;

// Create role
$role = Role::create(['name' => 'editor']);

// Add permission to role
$role->givePermissionTo('manage tickets');

// Add multiple permissions
$role->syncPermissions(['manage tickets', 'view tickets']);

// Remove permission
$role->revokePermissionTo('manage tickets');

// Get all permissions
$permissions = $role->getPermissionNames();
```

### Working with Permissions

```php
use Spatie\Permission\Models\Permission;

// Create permission
$permission = Permission::create(['name' => 'delete tickets']);

// Get all users with permission
$users = $permission->users()->get();

// Get all roles with permission
$roles = $permission->roles()->get();
```

## Controller Example

```php
<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function __construct()
    {
        // Ensure user is authenticated
        $this->middleware('auth');
        
        // Check permission per method
        $this->middleware('permission:view tickets')->only('index', 'show');
        $this->middleware('permission:manage tickets')->only('create', 'store', 'edit', 'update', 'destroy');
    }

    public function index()
    {
        $tickets = Ticket::all();
        return response()->json($tickets);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'topic' => 'required|string',
            'body' => 'required|string',
            'client_id' => 'required|exists:customers,id',
        ]);

        $ticket = Ticket::create($validated);
        return response()->json($ticket, 201);
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return response()->json(['message' => 'Ticket deleted']);
    }
}
```

## Database Tables

Permission package creates these tables:

- `roles` - Available roles
- `permissions` - Available permissions
- `role_has_permissions` - Role-permission mapping
- `model_has_roles` - User-role assignment
- `model_has_permissions` - User-permission assignment

## Command Line

```bash
# Create role
php artisan permission:create-role admin

# Create permission
php artisan permission:create-permission "manage tickets"

# Create both role and permission
php artisan permission:create-role manager
php artisan permission:create-permission "view tickets"
```

## Configuration

Edit `config/permission.php` to customize:

```php
'models' => [
    'permission' => Spatie\Permission\Models\Permission::class,
    'role' => Spatie\Permission\Models\Role::class,
],

'table_names' => [
    'roles' => 'roles',
    'permissions' => 'permissions',
    'role_has_permissions' => 'role_has_permissions',
    'model_has_roles' => 'model_has_roles',
    'model_has_permissions' => 'model_has_permissions',
],

'column_names' => [
    'model_morph_key' => 'model_id',
    'role_pivot_key' => 'role_id',
    'permission_pivot_key' => 'permission_id',
    'model_morph_type' => 'model_type',
],
```

## Caching

The package caches roles and permissions. Clear cache after changes:

```bash
# Clear all cache
php artisan cache:clear

# Or specifically permission cache
php artisan permission:cache-reset
```

## Important Notes

1. **User Model**: User model must use `HasRoles` trait (done in `App\Models\User`)
2. **Middleware Registration**: Middleware is registered in `bootstrap/app.php`
3. **Guards**: Default guard is 'web' (can be changed in config)
4. **Cache**: Permission checks are cached for performance

## Documentation

- [Spatie Laravel Permission Documentation](https://spatie.be/docs/laravel-permission)
- [GitHub Repository](https://github.com/spatie/laravel-permission)
