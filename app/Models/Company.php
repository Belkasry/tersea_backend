<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'email', 'sector_id', 'address', 'phone_number'];
    public function sector()
    {
        return $this->belongsTo(Sector::class, 'sector_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

}
