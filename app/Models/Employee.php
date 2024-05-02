<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function familyBackground(): HasMany
    {
        return $this->hasMany(FamilyBackground::class, 'employee_id', 'id');
    }

    public function workExperience(): HasMany
    {
        return $this->hasMany(WorkExperience::class, 'employee_id', 'id');
    }

    public function educationalAttainment(): HasMany
    {
        return $this->hasMany(EducationalAttainment::class, 'employee_id', 'id');
    }
}
