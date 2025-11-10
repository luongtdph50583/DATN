<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Club extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'logo',
        'field',
        'status',
        'manager_id',
        'email',
        'phone',
        'member_limit',
        'founded_at',
        'location',
        'rules',
        'deleted_reason'
    ];

    protected $casts = [
        'social_links' => 'array',
        'founded_at' => 'date',
        'description' => 'string',
    ];

    /** Người quản lý / chủ nhiệm CLB */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /** Thành viên CLB (qua bảng club_members) */
    public function members()
    {
        return $this->belongsToMany(User::class, 'club_members', 'club_id', 'member_id')
            ->withPivot(['role', 'status', 'note', 'joined_at'])
            ->withTimestamps();
    }

    /** Bài viết CLB */
    public function posts()
    {
        return $this->hasMany(Post::class, 'club_id');
    }

    /** Sự kiện CLB */
    public function events()
    {
        return $this->hasMany(Event::class, 'club_id');
    }

    /** Giao dịch quỹ */
    public function fundTransactions()
    {
        return $this->hasMany(FundTransaction::class, 'club_id');
    }

    /** Quỹ CLB */
    public function fund()
    {
        return $this->hasOne(Fund::class);
    }

    /** Tự động tạo quỹ khi tạo CLB mới */
    protected static function booted()
    {
        static::created(function ($club) {
            $club->fund()->create([
                'initial_balance' => 0,
                'balance' => 0,
            ]);
        });
    }

    /** Yêu cầu tham gia CLB */
    public function joinRequests()
    {
        return $this->hasMany(ClubJoinRequest::class, 'club_id');
    }
    public function documents()
    {
        return $this->hasMany(Document::class, 'clb_id');
    }


}