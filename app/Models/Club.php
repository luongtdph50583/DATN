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
<<<<<<< HEAD
     public function posts()
    {
        return $this->hasMany(Post::class);
    }

    
}
=======
    protected $casts = [
          'description' => 'string',
      ];
}
>>>>>>> 675c4230ea64f1035d7bdbe4c4f0ea59d095342f
