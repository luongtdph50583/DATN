<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Thời điểm hiện tại
        $now = Carbon::now();

        $members = [
            [
                'name' => 'Nguyễn Văn An',
                'email' => 'an.nguyen@example.com',
                'phone' => '0901234567',
                'address' => '123 Đường Hai Bà Trưng, Quận 1, TP.HCM',
                'status' => 1, // 1: Active
                'created_at' => $now->copy()->subDays(10),
                'updated_at' => $now->copy()->subDays(5),
            ],
            [
                'name' => 'Trần Thị Bình',
                'email' => 'binh.tran@example.com',
                'phone' => '0912345678',
                'address' => '456 Phố Bà Triệu, Quận Hoàn Kiếm, Hà Nội',
                'status' => 1,
                'created_at' => $now->copy()->subDays(20),
                'updated_at' => $now->copy()->subDays(15),
            ],
            [
                'name' => 'Lê Văn Cường',
                'email' => 'cuong.le@example.com',
                'phone' => '0987654321',
                'address' => '789 Ngõ Hùng Vương, TP. Đà Nẵng',
                'status' => 1,
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(1),
            ],
            [
                'name' => 'Phạm Thu Dung',
                'email' => 'dung.pham@example.com',
                'phone' => '0334567890',
                'address' => '101 Quốc Lộ 1A, TP. Cần Thơ',
                'status' => 0, // 0: Inactive/Banned
                'created_at' => $now->copy()->subDays(30),
                'updated_at' => $now->copy()->subDays(28),
            ],
            [
                'name' => 'Hoàng Minh Hải',
                'email' => 'hai.hoang@example.com',
                'phone' => '0778899000',
                'address' => '202 Đường Nguyễn Văn Linh, TP. Hải Phòng',
                'status' => 1,
                'created_at' => $now->copy()->subDays(7),
                'updated_at' => $now->copy()->subDays(7),
            ],
            [
                'name' => 'Vũ Kim Hương',
                'email' => 'huong.vu@example.com',
                'phone' => '0965432109',
                'address' => '303 Phố Trần Phú, TP. Nha Trang',
                'status' => 1,
                'created_at' => $now->copy()->subDays(15),
                'updated_at' => $now->copy()->subDays(10),
            ],
            [
                'name' => 'Đặng Trọng Khoa',
                'email' => 'khoa.dang@example.com',
                'phone' => '0887766554',
                'address' => '404 Lô Đất Mới, TP. Biên Hòa',
                'status' => 0,
                'created_at' => $now->copy()->subDays(45),
                'updated_at' => $now->copy()->subDays(40),
            ],
            [
                'name' => 'Bùi Thanh Lam',
                'email' => 'lam.bui@example.com',
                'phone' => '0932109876',
                'address' => '505 Hẻm 20, Quận 7, TP.HCM',
                'status' => 1,
                'created_at' => $now->copy()->subDays(3),
                'updated_at' => $now,
            ],
            [
                'name' => 'Mai Văn Nam',
                'email' => 'nam.mai@example.com',
                'phone' => '0945678901',
                'address' => '606 Đường Đống Đa, TP. Huế',
                'status' => 1,
                'created_at' => $now->copy()->subDays(12),
                'updated_at' => $now->copy()->subDays(8),
            ],
            [
                'name' => 'Dương Thúy Oanh',
                'email' => 'oanh.duong@example.com',
                'phone' => '0867890123',
                'address' => '707 Khu Dân Cư Mới, TP. Vinh',
                'status' => 1,
                'created_at' => $now->copy()->subDays(25),
                'updated_at' => $now->copy()->subDays(20),
            ],
        ];

        // Chèn dữ liệu vào bảng 'members'
        DB::table('members')->insert($members);
    }
}