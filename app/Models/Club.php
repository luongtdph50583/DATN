<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    protected $fillable = ['name', 'description', 'logo', 'field', 'status', 'manager_id'];

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function members()
    {
        return $this->hasMany(ClubMember::class, 'club_id');
    }



    public function events()
{
    return $this->hasMany(\App\Models\Event::class, 'club_id');
}

    protected $casts = [
          'description' => 'string',
      ];
      public function posts()
    {
        return $this->hasMany(Post::class);
    }
}


     

    


    

