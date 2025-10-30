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

// public function members()
// {
//     return $this->belongsToMany(Member::class, 'club_members')
//                 ->withPivot('role', 'joined_at')
//                 ->withTimestamps();
// }
public function members()
{
    return $this->belongsToMany(User::class, 'club_members', 'club_id', 'member_id')
                ->withPivot('role', 'joined_at', 'created_at')
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

    public function fund()
{
    return $this->hasOne(Fund::class);
}

    // Khi tạo CLB mới => tự tạo quỹ
    protected static function booted()
    {
        static::created(function ($club) {
            $club->fund()->create([
                'initial_balance' => 0,
                'balance' => 0,
            ]);
        });
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
    

}

