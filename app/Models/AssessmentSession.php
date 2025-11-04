<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AssessmentSession extends Model
{
    protected $fillable = [
        'session_id',
        'selected_languages',
        'question_ids',
        'answers',
        'timers',
        'score',
        'total_questions',
        'is_completed',
        'resume_path',
    ];

    protected $casts = [
        'selected_languages' => 'array',
        'question_ids' => 'array',
        'answers' => 'array',
        'timers' => 'array',
        'is_completed' => 'boolean',
    ];

    public function submission(): HasOne
    {
        return $this->hasOne(Submission::class);
    }
}
