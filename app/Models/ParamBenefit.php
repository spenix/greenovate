<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParamBenefit extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function emp_with_benefits(): HasMany
    {
        return $this->hasMany(Compensation::class, 'param_benefit_id', 'id');
    }
}
