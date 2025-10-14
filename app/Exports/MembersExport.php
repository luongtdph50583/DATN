<?php

namespace App\Exports;

use App\Models\Member;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MembersExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        // Kiểm tra và lấy dữ liệu từ bảng members
        $members = Member::all(); // Hoặc sử dụng query tùy chỉnh nếu cần

        // Nếu không có dữ liệu, trả về collection rỗng
        if ($members->isEmpty()) {
            return collect([]);
        }

        return $members->map(function ($member) {
            return [
                'ID' => $member->id,
                'Tên' => $member->name,
                'Email' => $member->email,
                'Số điện thoại' => $member->phone ?? 'N/A',
                'Địa chỉ' => $member->address ?? 'N/A',
                'Trạng thái' => ucfirst($member->status),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tên',
            'Email',
            'Số điện thoại',
            'Địa chỉ',
            'Trạng thái',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E0E0E0']]],
        ];
    }
}