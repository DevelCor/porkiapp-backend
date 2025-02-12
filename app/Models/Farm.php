<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    protected $fillable = [
        'name',
        'location',
        'invitation_code',
    ];

     protected $appends = ['role'];
     protected $hidden = ['pivot'];
 
     public function pigs()
     {
         return $this->hasMany(Pig::class);
     }
 
     public function users()
     {
         return $this->belongsToMany(User::class, 'users_farms')
                     ->withPivot('role')
                     ->withTimestamps();
     }

     public function getRoleAttribute()
     {
         return $this->pivot->role ?? null;
     }
}
