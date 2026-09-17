<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;   

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'status', 'priority', 
        'assignment_rules', 'assigned_to', 'created_by', 'due_date'
    ];

    protected $casts = [
        'assignment_rules' => 'array',
        'due_date' => 'datetime',
    ];
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
