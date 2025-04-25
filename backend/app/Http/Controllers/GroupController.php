<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::all();
        return response()->json($groups);
        // return view('groups.index', compact('groups'));
    }

    public function create(): View
    {
        return view('groups.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
        ]);

        $group = Group::create($validated);

        return response()->json([
            'message' => 'Group created successfully.',
            'group' => $group
        ]);
        // return redirect()->route('groups.index')
        //     ->with('success', 'Group created successfully.');
    }

    public function show(Group $group): View
    {
        $group->load('expenses');
        return view('groups.show', compact('group'));
    }

    public function edit(Group $group): View
    {
        return view('groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => 'required|max:255'
        ]);

        $group->update($validated);

        return response()->json([
            'message' => 'Group updated successfully.',
            'group' => $group
        ]);
        // return redirect()->route('groups.index')
        //     ->with('success', 'Group updated successfully.');
    }

    public function destroy(Group $group)
    {
        $group->delete();

       return response()->json([
            'message' => 'Group deleted successfully.'
       ]);
    }
}
