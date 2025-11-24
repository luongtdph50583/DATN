<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventBudgetItem extends Model
{
    protected $fillable = [
        'event_id', 'item_name', 'description', 'estimated_cost', 'actual_cost', 'type', 'order'
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'actual_cost'    => 'decimal:2',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}