<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory;

    use SoftDeletes;
    protected $fillable = [
        'title',
        'description',
        'file_name',
        'file_path',
        'file_type',
        'clb_id',
        'uploaded_by',
        'access_level'
        ,
        'tags'
    ];

    // Quan hệ với CLB
    public function club()
    {
        return $this->belongsTo(Club::class, 'clb_id');
    }

    // Quan hệ với người tải lên
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
