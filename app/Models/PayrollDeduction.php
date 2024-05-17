<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PayrollDeduction extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    public function deduction_details(): HasOne
    {
        return $this->hasOne(ParamDeduction::class, 'id', 'deduction_id');
    }
}
