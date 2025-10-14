<?php

   namespace App\Models;

   use Illuminate\Database\Eloquent\Model;

   class Notification extends Model
   {
       protected $keyType = 'string'; // Sử dụng UUID làm khóa chính
       public $incrementing = false;  // Tắt auto-increment

       protected $fillable = [
           'type', 'data', 'read_at'
       ];

       protected $casts = [
           'data' => 'array', 
           'read_at' => 'datetime',
       ];

       public function notifiable()
       {
           return $this->morphTo();
       }
   }