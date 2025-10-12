<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ClubJoinRequest extends Model
{
    protected $fillable = ['user_id', 'club_id', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }
}