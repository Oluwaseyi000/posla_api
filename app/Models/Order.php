<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory, UsesUuid;



    const INPROGRESS = 1;
    const COMPLETED = 2;
    const DISPUTED = 3;
    const CANCELLED = 4;
    const PENDING = 5;

    protected $fillable = ['owner_id', 'freelancer_id', 'type', 'type_data', 'reference_number', 'price', 'service_charge', 'delivery_time', 'revision_remaining', 'quantity', 'total_price', 'total_paid', 'project_starts_on', 'project_estimated_completion', 'project_ends_on', 'status'];

    protected function statusDisplay(){
        return  [
            $this::INPROGRESS => 'in_progress',
            $this::COMPLETED => 'completed',
            $this::DISPUTED => 'disputed',
        ];
    }

    protected $appends = ['status_display', 'types_data'];


    public function getStatusDisplayAttribute(){
        return  $this->statusDisplay()[$this->status];
    }

    public function getTypesDataAttribute(){
        return json_decode($this->type_data);

    }




}
