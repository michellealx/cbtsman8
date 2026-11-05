<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['teacher_id', 'name', 'code'];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
