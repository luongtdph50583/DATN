<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;
use Faker\Factory as Faker;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('vi_VN');
        
        $users = User::all();
        
        if ($users->isEmpty()) {
            return;
        }

        // Tạo 10 thông báo mẫu cho các users
        for ($i = 0; $i < 10; $i++) {
            $user = $users->random();
            
            Notification::create([
                'id' => \Illuminate\Support\Str::uuid()->toString(),
                'type' => 'App\Notifications\CustomNotification',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $user->id,
                'data' => [
                    'title' => $faker->sentence(4),
                    'message' => $faker->paragraph(2),
                    'type' => $faker->randomElement(['info', 'success', 'warning', 'error']),
                ],
                'read_at' => $faker->optional(0.3)->dateTimeBetween('-1 month', 'now'),
                'status' => 'sent',
                'batch_id' => $faker->optional(0.5)->uuid(),
            ]);
        }
    }
}