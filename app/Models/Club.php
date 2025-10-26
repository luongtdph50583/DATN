<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    protected $fillable = [
        'name', 'description', 'logo', 'field',
        'status', 'manager_id', 'email', 'phone', 'member_limit'
    ];

  public function manager()
{
    return $this->belongsTo(User::class, 'manager_id');
}

public function members()
{
    return $this->belongsToMany(User::class, 'club_members', 'club_id', 'member_id');
}
public function requests() {
    return $this->hasMany(ClubRequest::class);
}

}


