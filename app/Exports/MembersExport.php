<?php

namespace App\Exports;

use App\Models\Member;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MembersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $members;

    public function __construct($members)
    {
        $this->members = $members;
    }

    public function collection()
    {
        return $this->members;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Họ tên',
            'Email',
            'Số điện thoại',
            'CCCD',
            'Giới tính',
            'Ngày sinh',
            'Địa chỉ',
            'Khóa học',
            'Chuyên ngành',
            'Nơi cấp CCCD',
            'Ngày cấp CCCD',
            'Dân tộc',
            'Trạng thái',
            'Ngày tạo',
        ];
    }

    public function map($member): array
    {
        return [
            $member->id,
            $member->user->name ?? '—',
            $member->user->email ?? '—',
            $member->phone ?? '—',
            $member->citizen_id ?? '—',
            $this->formatGender($member->gender),
            $member->date_of_birth ? \Carbon\Carbon::parse($member->date_of_birth)->format('d/m/Y') : '—',
            $member->address ?? '—',
            $member->course ?? '—',
            $member->major ?? '—',
            $member->issued_place ?? '—',
            $member->issued_date ? \Carbon\Carbon::parse($member->issued_date)->format('d/m/Y') : '—',
            $member->ethnicity ?? '—',
            $member->status === 'active' ? 'Hoạt động' : 'Khóa',
            $member->created_at->format('d/m/Y H:i'),
        ];
    }

    private function formatGender($gender)
    {
        return match ($gender) {
            'male' => 'Nam',
            'female' => 'Nữ',
            'other' => 'Khác',
            default => '—',
        };
    }
}