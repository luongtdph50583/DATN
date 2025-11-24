<?php

namespace App\Exports;

use App\Models\Event;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EventsBudgetExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Event::with(['club', 'budgetItems'])->whereHas('budgetItems')->latest()->get();
    }

    public function headings(): array
    {
        return ['ID', 'Sự kiện', 'CLB', 'Tổng dự kiến', 'Xin cấp trường', 'CLB tự chi', 'Trạng thái'];
    }

    public function map($event): array
    {
        return [
            $event->id,
            $event->name,
            $event->club->name,
            $event->budgetItems->sum('estimated_cost'),
            $event->budgetItems->where('type', 'school_fund')->sum('estimated_cost'),
            $event->budgetItems->where('type', 'club_fund')->sum('estimated_cost'),
            $event->status == 'approved' ? 'Đã duyệt' : ($event->status == 'pending' ? 'Chờ duyệt' : 'Từ chối'),
        ];
    }
}