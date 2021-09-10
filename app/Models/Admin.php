<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin
// extends Authenticatable
{
    use HasFactory, UsesUuid;
}
