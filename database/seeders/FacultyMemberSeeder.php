<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\FacultyMember;

class FacultyMemberSeeder extends Seeder
{
    public function run(): void
    {
        $facultyData = [
            [
                'name' => 'Nguyễn Văn An',
                'gender' => 'male',
                'department' => 'Công nghệ thông tin',
                'title' => 'Giảng viên',
                'position' => 'Giảng viên',
            ],
            [
                'name' => 'Trần Thị Minh Anh',
                'gender' => 'female',
                'department' => 'Công nghệ thông tin',
                'title' => 'Giảng viên',
                'position' => 'Giảng viên',
            ],
            [
                'name' => 'Lê Văn Bình',
                'gender' => 'male',
                'department' => 'Kinh tế',
                'title' => 'Thạc sĩ',
                'position' => 'Giảng viên',
            ],
            [
                'name' => 'Phạm Thị Thu Hà',
                'gender' => 'female',
                'department' => 'Kế toán',
                'title' => 'Giảng viên',
                'position' => 'Giảng viên',
            ],
            [
                'name' => 'Hoàng Văn Cường',
                'gender' => 'male',
                'department' => 'Quản trị kinh doanh',
                'title' => 'Tiến sĩ',
                'position' => 'Trưởng bộ môn',
            ],
            [
                'name' => 'Vũ Thị Mai',
                'gender' => 'female',
                'department' => 'Marketing',
                'title' => 'Thạc sĩ',
                'position' => 'Giảng viên',
            ],
            [
                'name' => 'Đỗ Văn Hùng',
                'gender' => 'male',
                'department' => 'Cơ khí',
                'title' => 'Giảng viên',
                'position' => 'Giảng viên',
            ],
            [
                'name' => 'Nguyễn Thị Thanh Huyền',
                'gender' => 'female',
                'department' => 'Ngôn ngữ Anh',
                'title' => 'Giảng viên',
                'position' => 'Giảng viên',
            ],
            [
                'name' => 'Bùi Văn Long',
                'gender' => 'male',
                'department' => 'Điện – Điện tử',
                'title' => 'Thạc sĩ',
                'position' => 'Phó khoa',
            ],
            [
                'name' => 'Phan Thị Kim Oanh',
                'gender' => 'female',
                'department' => 'Tài chính – Ngân hàng',
                'title' => 'Giảng viên',
                'position' => 'Giảng viên',
            ],
            [
                'name' => 'Ngô Văn Phúc',
                'gender' => 'male',
                'department' => 'Xây dựng',
                'title' => 'Tiến sĩ',
                'position' => 'Trưởng khoa',
            ],
            [
                'name' => 'Trịnh Thị Lan',
                'gender' => 'female',
                'department' => 'Luật',
                'title' => 'Thạc sĩ',
                'position' => 'Giảng viên',
            ],
        ];

        foreach ($facultyData as $index => $faculty) {
            // 1️⃣ Tạo user
            $user = User::create([
                'name' => $faculty['name'],
                'email' => 'faculty' . ($index + 1) . '@university.edu.vn',
                'password' => Hash::make('123456'),
                'role' => 'manager', // hoặc admin nếu bạn muốn
                'status' => 'active',
            ]);

            // 2️⃣ Gán faculty_member
            FacultyMember::create([
                'user_id' => $user->id,
                'employee_code' => 'GV' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'department' => $faculty['department'],
                'title' => $faculty['title'],
                'position' => $faculty['position'],
                'office_phone' => '028-38' . rand(10000, 99999),
                'phone_personal' => '09' . rand(10000000, 99999999),
                'email_official' => 'gv' . ($index + 1) . '@university.edu.vn',
                'office_location' => 'Phòng ' . rand(101, 405),
                'verified' => true,
                'status' => 'active',
                'start_date' => now()->subYears(rand(1, 10)),
            ]);
        }
    }
}
