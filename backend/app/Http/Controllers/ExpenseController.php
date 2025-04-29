<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Group;
use App\Exports\ExpensesExport;
use App\Exports\ExpensesPdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ExpenseController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $expenses = $user->expenses()->with('group')->get();
        return response()->json($expenses);
        // return view('expenses.index', compact('expenses'));
    }

    public function create(): View
    {
        $user = Auth::user();
        $groups = Group::where('user_id', $user->id)->get();
        return view('expenses.create', compact('groups'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'group_id' => 'required|exists:groups,id',
            'date' => 'required|date'
        ]);
        $user = Auth::user();
        $validated['user_id'] = $user->id; // Add user_id to the validated data
        $data = Expense::create($validated);
        $expense = Expense::with('group')->find($data->id);
        return response()->json([
            'message' => 'Expense created successfully.',
            'expense' => $expense
        ]);
    }

    public function show(Expense $expense): View
    {

        if ($expense->user_id !== auth()->id) {
            abort(403, 'Unauthorized action.');
        };
        return view('expenses.show', compact('expense'));
    }
    public function edit(Expense $expense): View
    {
        if ($expense->user_id !== auth()->id) {
            abort(403, 'Unauthorized action.');
        };
        $groups = Group::where('user_id', auth()->id)->get();
        return view('expenses.edit', compact('expense', 'groups'));
    }
    public function update(Request $request, Expense $expense)
    {
        $user = Auth::user();
        if ($expense->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        };
        $validated = $request->validate([
            'name' => 'required|max:255',
            'amount' => 'required',
            'group_id' => 'required|exists:groups,id',
            'date' => 'required|date'
        ]);

        $expense->update($validated);
        $expense = Expense::with('group')->find($expense->id);
        return response()->json([
            'message' => 'Expense updated successfully.',
            'expense' => $expense
        ]);
        // return redirect()->route('expenses.index')
        //     ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $user = Auth::user();
        if ($expense->user_id !==  $user->id) {
            abort(403, 'Unauthorized action.');
        };
        $expense->delete();

        return response()->json([
            'message' => 'Expense deleted successfully.'
        ]);
    }

    public function export()
    {
        return Excel::download(new ExpensesExport, 'expenses.csv');
    }

    public function exportPdf()
    {
        $pdf = (new ExpensesPdf())->generate();
        return $pdf->download('expenses.pdf');
    }
}
