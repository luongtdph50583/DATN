<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventFundRequest extends Model
{
    
    use HasFactory;

protected $fillable = [
    'event_id', 'source_type', 'amount_requested', 'note', 'requested_by', 'status', 'approved_by','approved_amount'
];

    public function event()
{
    return $this->belongsTo(Event::class);
}

  public function user()
    {
        return $this->belongsTo(User::class, 'requested_by'); 
        // 'requested_by' là cột lưu user_id trong bảng event_fund_requests
    }
public function requestedBy()
{
    return $this->belongsTo(User::class, 'requested_by');
}

public function approvedBy()
{
    return $this->belongsTo(User::class, 'approved_by');
}


}