<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostUpdateLog extends Model
{
    protected $fillable = [
        'post_id',
        'changed_by',
        'changes',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }


    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}

