<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SuperAdmin extends Authenticatable
{
    use Notifiable;
    protected $table = 'superadmins';
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['last_login_at' => 'datetime', 'is_active' => 'boolean'];
    protected $guarded = ['id'];
}
