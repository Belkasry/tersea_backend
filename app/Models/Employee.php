<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','name', 'email', 'address', 'phone_number', 'birth_at', 'company_id'];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($employee) {
            if ($employee->user) {
                $employee->user->name = $employee->name;
                $employee->user->save();
            }
        });
    }

}
