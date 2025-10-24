<?php

namespace Database\Seeders;

use App\Models\FundTransaction;
use App\Models\Club;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FundTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clubs = Club::all();
        $users = User::all();
        
        if ($clubs->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No clubs or users found. Please run ClubSeeder and UserSeeder first.');
            return;
        }

        $categories = [
            'Hoạt động sự kiện',
            'Quà tặng',
            'Văn phòng phẩm',
            'Đào tạo',
            'Hỗ trợ thành viên',
            'Khác'
        ];

        $descriptions = [
            'Thu phí đăng ký sự kiện',
            'Chi phí tổ chức workshop',
            'Mua quà tặng cho thành viên',
            'Chi phí in ấn tài liệu',
            'Thu từ hoạt động gây quỹ',
            'Chi phí thuê địa điểm',
            'Mua đồng phục CLB',
            'Chi phí đi lại cho hoạt động',
            'Thu từ bán sản phẩm CLB',
            'Chi phí ăn uống cho sự kiện'
        ];

        foreach ($clubs as $club) {
            // Tạo 5-10 giao dịch cho mỗi CLB
            $transactionCount = rand(5, 10);
            
            for ($i = 0; $i < $transactionCount; $i++) {
                $type = rand(0, 1) ? 'income' : 'expense';
                $amount = $type === 'income' ? rand(50000, 500000) : rand(10000, 300000);
                $status = rand(0, 2) === 0 ? 'pending' : 'approved';
                
                FundTransaction::create([
                    'club_id' => $club->id,
                    'type' => $type,
                    'amount' => $amount,
                    'description' => $descriptions[array_rand($descriptions)],
                    'category' => $categories[array_rand($categories)],
                    'status' => $status,
                    'created_by' => $users->random()->id,
                    'approved_by' => $status === 'approved' ? $users->where('role', 'admin')->first()?->id : null,
                    'created_at' => now()->subDays(rand(1, 30)),
                    'updated_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }
        
        $this->command->info('Fund transactions seeded successfully!');
    }
}
