<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeletedComment extends Model
{
    use HasFactory, SoftDeletes;

   protected $fillable = ['comment_id', 'deleted_by', 'deleted_reason'];

}
