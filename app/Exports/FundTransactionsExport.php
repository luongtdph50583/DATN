<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FundTransactionsExport implements FromCollection, WithHeadings
{
    protected $transactions;
    protected $fundBalance;

    public function __construct($transactions, $fundBalance)
    {
        $this->transactions = $transactions; // Collection
        $this->fundBalance = $fundBalance;   // số dư hiện tại
    }

    public function collection()
    {
        $rows = $this->transactions->map(function ($tx) {
            // Xác định số tiền thực tế: thu dùng collected_amount, chi dùng amount
            $actualAmount = $tx->type === 'income' ? ($tx->collected_amount ?? 0) : $tx->amount;

            return [
                'ID' => $tx->id,
                'Loại' => $tx->type === 'income' ? 'Thu' : 'Chi',
                'Số tiền dự kiến' => $tx->amount,
                'Số tiền thực tế' => $actualAmount,
                'Ghi chú' => $tx->description,
                'Danh mục' => $tx->custom_category ?? $tx->category ?? '-',
                'Ngày tạo' => $tx->created_at->format('d/m/Y H:i'),
                'Người tạo' => $tx->creator->name ?? '-',
            ];
        });

        // Tính tổng thu, tổng chi theo số tiền thực tế
        $totalIncome = $this->transactions
            ->where('type', 'income')
            ->sum(fn($tx) => $tx->collected_amount ?? 0);

        $totalExpense = $this->transactions
            ->where('type', 'expense')
            ->sum('amount');

        // Thêm dòng tổng cộng
        $rows->push([
            'ID' => '',
            'Loại' => 'Tổng thu',
            'Số tiền dự kiến' => '',
            'Số tiền thực tế' => $totalIncome,
            'Ghi chú' => '',
            'Danh mục' => '',
            'Ngày tạo' => '',
            'Người tạo' => '',
        ]);

        $rows->push([
            'ID' => '',
            'Loại' => 'Tổng chi',
            'Số tiền dự kiến' => '',
            'Số tiền thực tế' => $totalExpense,
            'Ghi chú' => '',
            'Danh mục' => '',
            'Ngày tạo' => '',
            'Người tạo' => '',
        ]);

        $rows->push([
            'ID' => '',
            'Loại' => 'Số dư thực tế',
            'Số tiền dự kiến' => '',
            'Số tiền thực tế' => $this->fundBalance,
            'Ghi chú' => '',
            'Danh mục' => '',
            'Ngày tạo' => '',
            'Người tạo' => '',
        ]);

        return $rows;
    }

    public function headings(): array
    {
        return [
            'ID', 'Loại', 'Số tiền dự kiến', 'Số tiền thực tế', 'Ghi chú', 'Danh mục', 'Ngày tạo', 'Người tạo'
        ];
    }
}
