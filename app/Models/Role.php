<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasPermissions;

class Role extends Model
{
    use HasFactory, HasPermissions;

    protected $fillable = ['name', 'guard_name', 'description', 'level'];

    /**
     * Relasi many-to-many dengan User.
     * Role dimiliki oleh banyak user.
     */
    public function users()
    {
        return $this->belongsToMany(
        User::class, 
        'model_has_roles', 
        'role_id', 
        'model_id'
        );
    }

    /**
     * Relasi many-to-many dengan Permission.
     * Role dimiliki oleh banyak permission.
     */

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class, 
        'role_has_permissions', 
        'role_id', 
        'permission_id'
        );
    }
}
