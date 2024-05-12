<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Compensation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function benefit(): HasOne
    {
        return $this->hasOne(ParamBenefit::class, 'id', 'param_benefit_id');
    }
}
