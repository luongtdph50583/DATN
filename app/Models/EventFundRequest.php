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
    'event_id',
    'amount_requested',
    'approved_amount',
    'status',
    'note',
    'requested_by',
    'approved_by',
    'disbursed_by',
    'disbursement_date',
    'disbursement_proof',
    'rejection_reason',
];
protected $casts = [
    'amount_requested'   => 'decimal:2',
    'approved_amount'    => 'decimal:2',
    'disbursement_date'  => 'datetime',
    'created_at'         => 'datetime',
    'updated_at'         => 'datetime',
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
public function rejectedBy()
{
    return $this->belongsTo(User::class, 'rejected_by');
}
public function approvedBy()
{
    return $this->belongsTo(User::class, 'approved_by');
}
    public function disbursedBy()
    {
        return $this->belongsTo(User::class, 'disbursed_by');
    }

}