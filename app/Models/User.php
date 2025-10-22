<?php
    namespace App\Models;

    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;

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
         public function clubs()
    {
        return $this->belongsToMany(Club::class, 'club_members')
                    ->withPivot('role', 'joined_at')
                    ->withTimestamps();
    }

    // Một user có thể upload nhiều file


    public function managedClubs()
    {
        return $this->hasMany(Club::class, 'manager_id');
    }

    // ✅ Thành viên của các CLB (nếu có bảng trung gian ClubMember)
    public function memberships()
    {
        return $this->hasMany(ClubMember::class, 'user_id');
    }

    // ✅ Bài viết do người dùng đăng
    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    // ✅ Thông báo do người dùng tạo
    // public function notifications()
    // {
    //     return $this->hasMany(Notification::class, 'created_by');
    // }

    // ✅ Tài liệu do người dùng tải lên
    public function uploadedMedia()
    {
        return $this->hasMany(Media::class, 'uploaded_by');
    }

    // ✅ Sự kiện do người dùng tạo
    public function events()
    {
        return $this->hasMany(Event::class, 'created_by');

    }
}
