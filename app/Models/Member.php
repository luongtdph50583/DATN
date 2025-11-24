<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'gender',
        'student_code',
        'date_of_birth',
        'address',
        'course',
        'major',
        'citizen_id',
        'issued_date',
        'issued_place',
        'ethnicity',
        'phone',
        'status'
    ];

    protected $casts = [
        'status' => 'string',
    ];
    
    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

     public function clubMembers()
    {
        return $this->hasMany(ClubMember::class, 'member_id');
    }
    // App/Models/Member.php
public function clubs()
{
    return $this->belongsToMany(
        Club::class,
        'club_members',        
        'member_id',        
        'club_id'             
    )->withPivot('created_at', 'updated_at', 'role'); 
}
  
 public function clubMemberships()
    {
        return $this->belongsToMany(Club::class, 'club_members', 'member_id', 'club_id')
                    ->withPivot('role', 'status')
                    ->withTimestamps();
    }
    // Bài viết của member (hasMany)
    // Bài viết của thành viên thông qua user
    public function posts()
    {
        return $this->hasManyThrough(
            Post::class,   // Model cuối cùng
            User::class,   // Model trung gian
            'id',          // Khóa chính của User (trong bảng users)
            'user_id',     // Khóa ngoại trong bảng posts
            'user_id',     // Khóa ngoại của Member trỏ tới User
            'id'           // Khóa chính của Member
        );
    }

    // Comment của member (hasMany)
   public function comments()
    {
        return $this->hasManyThrough(
            Comment::class,
            User::class,
            'id',
            'user_id',
            'user_id',
            'id'
        );
    }

       public function isInClub($clubId)
    {
        return $this->clubs()->where('clubs.id', $clubId)->exists();
    }

    // Helper: check xem member có tham gia event nào
    public function hasEvent($eventId)
    {
        return $this->events()->where('events.id', $eventId)->exists();
    }

}

