<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Group;
use App\Exports\ExpensesExport;
use App\Exports\ExpensesPdf;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\StoreExpenseRequest;
use Illuminate\Contracts\Cache\Store;

class ExpenseController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                throw new \Exception('User not authenticated');
            }

            $expenses = $user->expenses()->with('group')->get();
            return ApiResponse::success($expenses, "Expenses fetched successfully");
        } catch (\Exception $e) {
            // \Log::error('Error fetching expenses: ' . $e->getMessage());
            return ApiResponse::error('Failed to fetch expenses', [], 500);
        }
    }

    public function create(): View
    {
        $user = Auth::user();
        $groups = Group::where('user_id', $user->id)->get();
        return view('expenses.create', compact('groups'));
    }
    public function store(StoreExpenseRequest $request)
    {
        try {
            $validated = $request->validated();
            
            $user = Auth::user();
            if (!$user) {
                throw new \Exception('User not authenticated');
            }
            
            $validated['user_id'] = $user->id;
            $data = Expense::create($validated);
            $expense = Expense::with('group')->find($data->id);
            
            return ApiResponse::success($expense, "Expense created successfully");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::error('Validation error', $e->errors(), 422);
        } catch (\Exception $e) {
            // \Log::error('Error creating expense: ' . $e->getMessage());
            return ApiResponse::error('Failed to create expense', [], 500);
        }
    }

    public function show(Expense $expense): View
    {
        try {
            $user = Auth::user();
            if (!$user) {
                throw new \Exception('User not authenticated');
            }

            if ($expense->user_id !== $user->id) {
                throw new \Exception('Unauthorized action');
            }

            return view('expenses.show', compact('expense'));
        } catch (\Exception $e) {
            // \Log::error('Error showing expense: ' . $e->getMessage());
            abort(403, $e->getMessage());
        }
    }
    public function edit(Expense $expense): View
    {
        try {
            $user = Auth::user();
            if (!$user) {
                throw new \Exception('User not authenticated');
            }

            if ($expense->user_id !== $user->id) {
                throw new \Exception('Unauthorized action');
            }

            $groups = Group::where('user_id', $user->id)->get();
            return view('expenses.edit', compact('expense', 'groups'));
        } catch (\Exception $e) {
            // \Log::error('Error editing expense: ' . $e->getMessage());
            abort(403, $e->getMessage());
        }
    }
    public function update(StoreExpenseRequest $request, Expense $expense)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                throw new \Exception('User not authenticated');
            }
            
            if ($expense->user_id !== $user->id) {
                throw new \Exception('Unauthorized action');
            }
            
            $validated = $request->validated();

            $expense->update($validated);
            $expense = Expense::with('group')->find($expense->id);
            
            return ApiResponse::success($expense, "Expense updated successfully");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::error('Validation error', $e->errors(), 422);
        } catch (\Exception $e) {
            // \Log::error('Error updating expense: ' . $e->getMessage());
            return ApiResponse::error('Failed to update expense', [], 500);
        }
        // return redirect()->route('expenses.index')
        //     ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                throw new \Exception('User not authenticated');
            }
            
            if ($expense->user_id !== $user->id) {
                throw new \Exception('Unauthorized action');
            }
            
            $expense->delete();
            return ApiResponse::success([], "Expense deleted successfully");
        } catch (\Exception $e) {
            // \Log::error('Error deleting expense: ' . $e->getMessage());
            return ApiResponse::error('Failed to delete expense', [], 500);
        }
    }

    public function export()
    {
        try {
            return Excel::download(new ExpensesExport, 'expenses.csv');
        } catch (\Exception $e) {
            // \Log::error('Error exporting expenses to CSV: ' . $e->getMessage());
            return ApiResponse::error('Failed to export CSV', [], 500);
        }
    }

    public function exportPdf()
    {
        try {
            $pdf = (new ExpensesPdf())->generate();
            return $pdf->download('expenses.pdf');
        } catch (\Exception $e) {
            // \Log::error('Error exporting expenses to PDF: ' . $e->getMessage());
            return ApiResponse::error('Failed to export PDF', [], 500);
        }
    }
}
