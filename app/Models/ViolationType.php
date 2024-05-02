<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ViolationType extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function category(): HasOne
    {
        return $this->hasOne(ViolationCategory::class, 'id', 'violation_category_id');
    }
}
