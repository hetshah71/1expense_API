<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Auth;

class ExpensesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $user = Auth::user();
        return Expense::where('user_id', $user->id)
            ->with('group')
            ->get()
            ->map(function ($expense) {
                return [
                    'Name' => $expense->name,
                    'Amount' => $expense->amount,
                    'Group' => $expense->group->name,
                    'Date' => $expense->date,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Name',
            'Amount',
            'Group',
            'Date',
        ];
    }
} 