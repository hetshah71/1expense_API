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
        try {
            $user = Auth::user();
            if (!$user) {
                throw new \Exception('User not authenticated');
            }

            return Expense::where('user_id', $user->id)
                ->with('group')
                ->get()
                ->map(function ($expense) {
                    try {
                        return [
                            'Name' => $expense->name,
                            'Amount' => $expense->amount,
                            'Group' => $expense->group->name,
                            'Date' => $expense->date,
                        ];
                    } catch (\Exception $e) {
                        // \Log::error('Error mapping expense: ' . $e->getMessage());
                        return [
                            'Name' => 'Error',
                            'Amount' => 0,
                            'Group' => 'Error',
                            'Date' => now(),
                        ];
                    }
                });
        } catch (\Exception $e) {
            // \Log::error('Error fetching expenses: ' . $e->getMessage());
            return collect([]);
        }
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