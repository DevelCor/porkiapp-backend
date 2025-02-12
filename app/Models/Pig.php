<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pig extends Model
{
    protected $fillable = [
        'gender',
        'age',
        'weight',
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

    // Relationship: Parent of the pig
    public function parent()
    {
        return $this->belongsTo(Pig::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Pig::class, 'parent_id');
    }
}
