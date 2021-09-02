<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoryNotificationSubscription extends Model
{
    use UsesUuid, HasFactory;

    protected $fillable = ['user_id', 'category_id'];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
