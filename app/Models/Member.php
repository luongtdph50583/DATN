<?php

     namespace App\Models;

     use Illuminate\Database\Eloquent\Model;

     class Member extends Model
     {
         protected $fillable = [
             'name', 'email', 'phone', 'address', 'status',
         ];

         protected $casts = [
             'status' => 'string',
         ];
         public function user()
{
    return $this->belongsTo(User::class);
}

public function clubs()
{
    return $this->belongsToMany(Club::class, 'club_members')
                ->withPivot('role', 'joined_at')
                ->withTimestamps();
}

     }