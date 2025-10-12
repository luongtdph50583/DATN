<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ClubRequest extends Model
{
    protected $fillable = ['name', 'description', 'field', 'status'];
}