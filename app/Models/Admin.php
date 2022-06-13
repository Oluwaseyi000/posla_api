<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Admin
// extends Authenticatable
{
    use HasFactory, UsesUuid, Notifiable, HasApiTokens;

    protected $fillable = ['first_name', 'last_name', 'email', 'phone', 'password', 'phone'];
}
