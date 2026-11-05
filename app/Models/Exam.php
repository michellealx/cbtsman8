<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'teacher_id',
        'title',
        'subject',
        'description',
        'duration_minutes',
        'status',
        'require_seb',
        'randomize_questions',
        'start_time',
        'end_time',
        'deadline',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'deadline' => 'datetime',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class)
                    ->orderByRaw("FIELD(type, 'multiple_choice', 'essay')")
                    ->orderBy('order')
                    ->orderBy('id');
    }

    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function sessions()
    {
        return $this->hasMany(ExamSession::class);
    }
}
