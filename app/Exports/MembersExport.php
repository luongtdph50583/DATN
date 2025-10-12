<?php

namespace App\Exports;

use App\Models\Club;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class MembersExport implements FromCollection, WithHeadings, WithStyles
{
    protected $clubs;

    public function __construct($clubs)
    {
        $this->clubs = $clubs;
    }

    public function collection()
    {
        return $this->clubs->flatMap(function ($club) {
            return $club->members->map(function ($member) use ($club) {
                // Kiểm tra và chuyển đổi joined_at thành Carbon nếu cần
                $joinedAt = $member->joined_at;
                if ($joinedAt && !$joinedAt instanceof Carbon) {
                    $joinedAt = Carbon::parse($joinedAt);
                }
                $joinedAtFormatted = $joinedAt ? $joinedAt->format('d/m/Y') : 'Chưa xác định';

                return [
                    'ID' => $member->user->id,
                    'Tên' => $member->user->name,
                    'Email' => $member->user->email,
                    'Vai trò' => ucfirst($member->role),
                    'CLB' => $club->name,
                    'Ngày tham gia' => $joinedAtFormatted,
                ];
            });
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tên',
            'Email',
            'Vai trò',
            'CLB',
            'Ngày tham gia',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E0E0E0']]],
        ];
    }
}