<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTransaction extends Model
{
    use UsesUuid, HasFactory;

    protected $fillable = ['owner_id', 'freelancer_id', 'order_id', 'reference', 'ext_reference', 'transaction_channel', 'transaction_metadata',
       'amount_paid', 'amount_expected', 'payment_type', 'currency', 'status'];
}
