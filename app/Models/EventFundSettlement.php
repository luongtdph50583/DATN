<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventFundSettlement extends Model
{
    protected $fillable = [
        'event_id', 'total_spent', 'details', 'receipts', 
        'difference', 'status', 'reviewed_by', 'reviewed_at','fund_request_id'
    ];

    protected $casts = [
        'details' => 'array',
        'receipts' => 'array',
        'reviewed_at' => 'datetime',
    ];

        public function fundRequest()
    {
        return $this->belongsTo(EventFundRequest::class, 'fund_request_id');
    }
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
    

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}