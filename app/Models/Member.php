<?php

     namespace App\Models;

     use Illuminate\Database\Eloquent\Model;
     use App\Models\User;

     class Member extends Model
     {
         protected $fillable = [
            'user_id',  
            'gender',
            'date_of_birth',
            'address',
            'course',
            'major',
            'citizen_id',
            'issued_date',
            'issued_place',
            'ethnicity',
            'phone',
            'status'
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