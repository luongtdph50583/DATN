<?php

     namespace App\Models;

     use Illuminate\Database\Eloquent\Model;

     class Event extends Model
     {
         protected $fillable = [
             'club_id', 'name', 'description', 'event_date', 'location', 'status', 'created_by',
         ];

         protected $casts = [
             'event_date' => 'datetime',
             'status' => 'string',
         ];

         // Quan hệ với Club
         public function club()
         {
             return $this->belongsTo(Club::class);
         }

         // Quan hệ với User (người tạo)
         public function createdBy()
         {
             return $this->belongsTo(User::class, 'created_by');
         }
     }