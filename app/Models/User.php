<?php
   namespace App\Models;

   use Illuminate\Foundation\Auth\User as Authenticatable;
   use Illuminate\Notifications\Notifiable;

   class User extends Authenticatable
   {
       use Notifiable;

       protected $fillable = [
           'name', 'email', 'password', 'role', 'status', 'phone', 'avatar', 'student_id', 'department', 'email_verified_at',
       ];

       protected $casts = [
           'email_verified_at' => 'datetime',
           'role' => 'string',
           'status' => 'string',
       ];

       protected $hidden = [
           'password', 'remember_token',
       ];
   }