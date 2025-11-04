<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fund extends Model
{
    protected $fillable = ['club_id', 'balance', 'initial_balance'];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function transactions()
    {
        return $this->hasMany(FundTransaction::class);
    }
}
