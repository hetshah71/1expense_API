<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with('group')->get();
        return response()->json($expenses);
        // return view('expenses.index', compact('expenses'));
    }

    public function create(): View
    {
        $groups = Group::all();
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

        $data = Expense::create($validated);
        $expense = Expense::with('group')->find($data->id);
        return response()->json([
            'message' => 'Expense created successfully.',
            'expense' => $expense
        ]);
    }

    public function show(Expense $expense): View
    {
        return view('expenses.show', compact('expense'));
    }
    public function edit(Expense $expense): View
    {
        $groups = Group::all();
        return view('expenses.edit', compact('expense', 'groups'));
    }
    public function update(Request $request, Expense $expense)
    {

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
        $expense->delete();

        return response()->json([
            'message' => 'Expense deleted successfully.'
        ]);
    }
}
