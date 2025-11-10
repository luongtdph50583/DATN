<?php
    namespace App\Models;

    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;
    use App\Models\Member;
    class User extends Authenticatable
    {
        use Notifiable;


    protected $fillable = [
            'name', 'email', 'password', 'role', 'status', 'phone', 'student_id', 'department', 'avatar',
        ];

        protected $hidden = [
            'password', 'remember_token',
        ];

        protected $casts = [
            'email_verified_at' => 'datetime',
            'status' => 'string',
        ];

// public function managedClubs()
// {
//     return $this->hasMany(Club::class, 'manager_id');
// }

public function memberProfile()
{
    return $this->hasOne(Member::class);
}
public function user()
{
    return $this->belongsTo(User::class);
}
    public function member()
    {
        return $this->hasOne(Member::class, 'user_id');
    }

    public function clubs()
{
    return $this->belongsToMany(Club::class, 'club_members', 'member_id', 'club_id')
                ->withPivot(['role', 'joined_at'])
                ->withTimestamps();
}

    // 🔹 Các yêu cầu tham gia CLB
    public function clubJoinRequests()
    {
        return $this->hasMany(ClubJoinRequest::class, 'user_id');
    }
        // 🔹 CLB mà user quản lý
    public function managedClubs()
    {
        return $this->hasMany(Club::class, 'manager_id');
    }
public function clubRequests()
{
    return $this->hasMany(ClubRequest::class);
}


    /**
     * Quan hệ với Events (nếu có bảng registrations)
     */
public function events()
{
    return $this->hasMany(Event::class, 'organizer_id')
                ->orWhereHas('registrations', function($q) {
                    $q->where('user_id', $this->id);
                });
}

/**
 * Quan hệ với Posts
 */
public function posts()
{
    return $this->hasMany(Post::class);
}
    // User.php


    public function facultyMember()
    {
        return $this->hasOne(FacultyMember::class);
    }


}
