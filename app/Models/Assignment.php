<?php

namespace App\Models;

use App\Models\Unit;
use App\Models\Employee;
use App\Models\Transportation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Assignment extends Model
{
    use HasFactory;
    
    protected $guarded = ['id'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function transportation()
    {
        return $this->belongsTo(Transportation::class);
    }
}
