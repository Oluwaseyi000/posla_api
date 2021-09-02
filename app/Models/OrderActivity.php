<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderActivity extends Model
{
    use UsesUuid, HasFactory;

    const REQUIREMENT_FILLED = 'REQUIREMENT_FILLED';
    CONST ORDER_STARTED = 2;
    CONST CONVERSATION = 3;
    CONST DOWNLOAD_READY = 4;
    CONST ORDER_DELIVERED = 4;
    CONST ORDER_ACCEPTED = 4;
    const REVISION_REQUESTED = 21;
    const ORDER_MODIFIED = 12;
    const ORDER_CANCELLED = 12;

}
