<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payroll extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    public function deductions(): HasMany
    {
        return $this->hasMany(PayrollDeduction::class, 'payroll_id', 'id');
    }

    public function compensations(): HasMany
    {
        return $this->hasMany(PayrollCompensation::class, 'payroll_id', 'id');
    }
}
