<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Proposal extends Model
{
    use HasFactory, UsesUuid;

    const SUBMITTED = 1;
    const ACCEPTED = 2;
    const SHORTLISTED = 3;
    const REJECTED = 0;

    protected function statusDisplay(){
        return  [
            $this::SUBMITTED => 'submitted',
            $this::ACCEPTED => 'accepted',
            $this::REJECTED => 'rejected',
            $this::SHORTLISTED => 'shortlisted',
        ];
    }  

    protected $appends = ['status_display'];


    protected $fillable = ['project_id', 'user_id', 'comment', 'amount', 'deposit', 'status'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getStatusDisplayAttribute(){
        return  $this->statusDisplay()[$this->status];
    }
}
