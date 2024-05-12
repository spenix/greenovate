<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EmployeeLeave extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function leave_type(): HasOne
    {
        return $this->hasOne(LeaveType::class, 'id', 'leave_type_id');
    }

    public function leave_entitlement(): HasOne
    {
        return $this->hasOne(LeaveEntitlement::class, 'id', 'leave_entitlement_id');
    }
}
