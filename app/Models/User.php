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
    // // ✅ CLB do người dùng quản lý
    // public function managedClubs(): HasMany
    // {
    //     return $this->hasMany(Club::class, 'manager_id');
    // }

    // // ✅ Thành viên của các CLB (nếu có bảng trung gian ClubMember)
    // public function memberships(): HasMany
    // {
    //     return $this->hasMany(ClubMember::class, 'user_id');
    // }

    // // ✅ Bài viết do người dùng đăng
    // public function posts(): HasMany
    // {
    //     return $this->hasMany(Post::class, 'user_id');
    // }

    // // ✅ Thông báo do người dùng tạo
    // public function notifications()
    // {
    //     return $this->hasMany(Notification::class, 'created_by');
    // }

    // // ✅ Tài liệu do người dùng tải lên
    // public function uploadedMedia()
    // {
    //     return $this->hasMany(Media::class, 'uploaded_by');
    // }

    // // ✅ Sự kiện do người dùng tạo
    // public function events()
    // {
    //     return $this->hasMany(Event::class, 'created_by');

    // }
}