<?php

namespace App\Exports;

use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf as DomPDF;

class ExpensesPdf
{
    public function generate()
    {
        $user = Auth::user();
        $expenses = Expense::where('user_id', $user->id)
            ->with('group')
            ->get();

        $data = [
            'expenses' => $expenses,
            'user' => $user,
            'total' => $expenses->sum('amount'),
            'date' => now()->format('Y-m-d')
        ];

        $pdf = DomPDF::loadView('pdf.expenses', $data);
        return $pdf;
    }
}
