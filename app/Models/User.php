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
         public function managedClubs()
        {
            return $this->hasMany(Club::class, 'manager_id');
        }

        public function memberships()
        {
            return $this->hasMany(ClubMember::class, 'user_id');
        }
    }