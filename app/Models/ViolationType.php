<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ViolationType extends Model
{
    use HasFactory;


    protected $guarded = [];

    public function category(): HasOne
    {
        return $this->hasOne(ViolationCategory::class, 'id', 'violation_category_id');
    }
}
