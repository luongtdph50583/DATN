<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasFactory;

     protected $fillable = ['name', 'description', 'logo', 'field', 'status', 'manager_id'];
      
    // 👤 Người phụ trách (chủ nhiệm CLB)
    public function leader()
    {
        return $this->belongsTo(User::class, 'manager_id'); 
        // manager_id là khóa ngoại trong bảng clubs trỏ tới id của users
    }

// Trong Club model
public function members()
{
    return $this->belongsToMany(User::class, 'club_members', 'club_id', 'user_id')
                ->withPivot('role', 'created_at')
                ->withTimestamps();
}



    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    public function events()
    {
        return $this->hasMany(Event::class, 'club_id');
    }

    public function fundTransactions()
    {
        return $this->hasMany(FundTransaction::class);
    }



    protected $casts = [
          'description' => 'string',
      ];

    // 🔗 Các yêu cầu tham gia CLB
    public function joinRequests()
    {
        return $this->hasMany(ClubJoinRequest::class);
    }

    // 🧑‍🤝‍🧑 Thành viên CLB (nếu bạn có bảng trung gian)
    // public function members()
    // {
    //     return $this->hasMany(ClubMember::class);
    // }
     public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id'); 
        // manager_id là khóa ngoại trong bảng clubs trỏ tới id của users
    }

}

// class Club extends Model
// {
//     protected $fillable = ['name', 'description', 'logo', 'field', 'status', 'manager_id'];

//       public function manager()
//     {
//         return $this->belongsTo(User::class, 'manager_id'); 
//         // manager_id là khóa ngoại trong bảng clubs trỏ tới id của users
//     }

// // Trong Club model
// public function members()
// {
//     return $this->belongsToMany(User::class, 'club_members', 'club_id', 'user_id')
//                 ->withPivot('role', 'created_at')
//                 ->withTimestamps();
// }



//      public function posts()
//     {
//         return $this->hasMany(Post::class);
//     }
//     public function events()
//     {
//         return $this->hasMany(Event::class, 'club_id');
//     }


//     protected $casts = [
//           'description' => 'string',
//       ];

// }
