<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = ['teacher_id', 'name'];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function examSessions()
    {
        return $this->hasMany(ExamSession::class);
    }
}
