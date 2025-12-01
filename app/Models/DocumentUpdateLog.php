<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentUpdateLog extends Model
{
    protected $fillable = ['document_id', 'changed_by', 'changes'];

    protected $casts = [
        'changes' => 'array',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function documentWithDefault()
    {
        return $this->belongsTo(Document::class, 'document_id')
            ->withDefault([
                'title' => 'Tài liệu đã xóa',
                'clb_id' => null,
            ]);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}

