<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fund extends Model
{
    protected $fillable = ['club_id', 'balance', 'initial_balance'];

  public function transactions()
    {
        return $this->hasMany(FundTransaction::class, 'club_id', 'club_id')
                    ->orderBy('created_at', 'desc');
    }

    // Quan hệ đến CLB
    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}
