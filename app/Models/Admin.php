<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable implements MustVerifyEmail
// extends Authenticatable
{
    use HasFactory, UsesUuid, Notifiable, HasApiTokens;

    protected $fillable = ['first_name', 'last_name', 'email', 'phone', 'password', 'phone'];
}
