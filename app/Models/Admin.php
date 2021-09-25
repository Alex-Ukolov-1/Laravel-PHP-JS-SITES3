<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Traits\CRUDFunctions;
use App\Models\Traits\HasPassword;
use Auth;
use Hash;

class Admin extends Authenticatable
{
    use Notifiable;
    use CRUDFunctions;
    use HasPassword;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'status',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
