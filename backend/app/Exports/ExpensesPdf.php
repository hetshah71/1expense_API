<?php

namespace App\Exports;

use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf as DomPDF;

class ExpensesPdf
{
    public function generate()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                throw new \Exception('User not authenticated');
            }

            $expenses = Expense::where('user_id', $user->id)
                ->with('group')
                ->get();

            $data = [
                'expenses' => $expenses,
                'user' => $user,
                'total' => $expenses->sum('amount'),
                'date' => now()->format('Y-m-d')
            ];

            try {
                $pdf = DomPDF::loadView('pdf.expenses', $data);
                return $pdf;
            } catch (\Exception $e) {
                // \Log::error('Error generating PDF: ' . $e->getMessage());
                throw new \Exception('Failed to generate PDF report');
            }
        } catch (\Exception $e) {
            // \Log::error('Error preparing PDF data: ' . $e->getMessage());
            throw $e;
        }
    }
}
