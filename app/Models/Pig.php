<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pig extends Model
{
    protected $fillable = [
        'gender',
        'weight',
        'birth_date',
        'parent_id',
        'birth_code',
        'user_id',
        'farm_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    // Relación: Cerdo padre
    public function parent()
    {
        return $this->belongsTo(Pig::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Pig::class, 'parent_id');
    }

    public function treatments()
    {
        return $this->hasMany(PigTreatment::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
