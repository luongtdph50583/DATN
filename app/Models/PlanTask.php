<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanTask extends Model
{
    protected $fillable = [
        'plan_id', 'title', 'description', 'due_date',
        'priority', 'status', 'assigned_to'
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(ClubPlan::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}